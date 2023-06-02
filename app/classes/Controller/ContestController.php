<?php

namespace ContestApp\Controller;

use \aalfiann\JSON;
use \ContestApp\Http\{Header, StatusCode};
use \ContestApp\Resource\{MimeType, CsvDataLoader};
use \ContestApp\Model\{ContestAttribute, ContestModel, AccountAttribute, AccountModel};
use \ContestApp\ViewPage\{ContestListPage, ContestCreatePage, ContestInfoPage, ErrorPage};
use \ContestApp\System\Time;

/**
 * 공모전 관련 요청 컨트롤러
 */
class ContestController {

  // POST된 파라미터 중 ContestController에서 관리할 값들을 가져온다.
  public static function parsePostedParams(): Array {

    $decoded = [];
    foreach ($_POST as $name => $value) {
      $decoded[strtolower(trim($name))] = urldecode($value);
    }
    foreach (ContestAttribute::cases() as $attribute) {
      $attribute = $attribute->value;
      $decoded[$attribute] = ContestAttribute::trimValue($attribute, $decoded[$attribute] ?? "");
    }

    return $decoded;

  }

  /**
   * 리디렉션
   *
   * @param String $path 리디렉션 경로 (/contest*)
   * @param bool $permanent 영구적 리디렉션인지
   */
  public static function redirect(String $path, bool $permanent = false): void {

    $location = sprintf("/contest%s", $path);
    $status = $permanent ? StatusCode::MOVED_PERMANENTLY : StatusCode::MOVED_TEMPORARILY;

    StatusCode::setServerStatusCode($status);
    Header::setServerHeader(Header::LOCATION, $location);

  }

  // 공모전 목록 페이지로 리디렉션
  public static function redirectToList(): void {
    self::redirect("/list", true);
  }

  // 로그인 페이지로 리디렉션
  public static function redirectToSignin(): void {

    StatusCode::setServerStatusCode(StatusCode::MOVED_TEMPORARILY);
    Header::setServerHeader(Header::LOCATION, "/account/signin");

  }

  // 참가신청을 검증
  private static function validateApplication(int $code, String $phone): int {

    $applicantInfo = AccountModel::getUserInfoByPhone($phone);
    $contestInfo   = ContestModel::getContestInfoByCode($code);

    $creatorPhone  = $contestInfo[AccountAttribute::Phone->value];
    $creatorInfo   = AccountModel::getUserInfoByPhone($creatorPhone);

    $currentTime = time();

    // 모집 시작일이 지났는지 확인
    $beginning = sprintf("%s 00:00:00", $contestInfo[ContestAttribute::Beginningdate->value]);
    $beginning = Time::StrTimestampToInt($beginning);
    if ($beginning > $currentTime) {
      return 11;
    }

    // 마감일이 지났는지 확인
    $deadline = sprintf("%s 00:00:00", $contestInfo[ContestAttribute::Deadline->value]);
    $deadline = Time::StrTimestampToInt($deadline);
    if ($deadline <= $currentTime) {
      return 12;
    }

    // 이미 모집 종료된 상태인지 확인
    if ((bool)$contestInfo[ContestAttribute::Done->value]) {
      return 12;
    }

    // 학교가 일치하는지 확인
    if ((int)$contestInfo[ContestAttribute::Intramural->value]) {
      if (
        (int)$applicantInfo[AccountAttribute::College->value] !=
        (int)$creatorInfo[AccountAttribute::College->value]
      ) {
        return 14;
      }
    }

    // 평가 점수가 일치하는지 확인
    $ratingCondition = $contestInfo[ContestAttribute::Rating->value];
    $memberRating = $applicantInfo[AccountAttribute::Rating->value];
    $applicable = match ($ratingCondition) {
      1       => ($memberRating > 5),
      2       => ($memberRating >= -1),
      0, 3    => true
    };
    if (!$applicable) {
      return 15;
    }

    return 1;

  }

  // 공모전 참가신청 요청 처리
  public static function handleApply(): void {

    if (!AccountController::signedIn()) {     // 로그인하지 않은 상태면 Bad Request 응답
      StatusCode::setServerStatusCode(StatusCode::BAD_REQUEST);
      return;
    }

    $result = -1;
    $apply  = 1;    // 참가신청 요청이면 1, 참가취소 요청이면 0
    $phone = AccountController::parseAccessToken()[ContestAttribute::Phone->value];
    $code = self::parsePostedParams()[ContestAttribute::Code->value];

    self::autoCloseContest($code);

    if (count(ContestModel::getContestInfoByCode($code)) !== 0) {   // 일치하는 공모전이 있으면
      if (!ContestModel::isCreatedBy($code, $phone)) {              // 본인이 등록한 공모전이 아니면
        if (ContestModel::appliedInContest($code, $phone)) {        // 이미 참가 신청했으면
          $apply = 0;
          $result = ContestModel::cancleApplication($code, $phone) ? 1 : 4;
        } else {
          $apply = 1;
          $result = self::validateApplication($code, $phone);   // 참가 조건을 검증
          if ($result == 1) {
            $result = ContestModel::applyInContest($code, $phone) ? 1 : 4;
          }
        }
      } else {
        $result = 2;     // 본인이 등록한 공모전이면 2
      }
    } else {
      $result = 3;       // 없는 공모전 코드면 3
    }

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_JSON->value);
    printf("%s",
      (new JSON)->encode([ "status" => $result, "apply" => $apply ])
    );

  }

  // 공모전 등록 요청 처리
  public static function handleCreate(): void {

    if (!AccountController::signedIn()) {     // 로그인하지 않은 상태면 Bad Request 응답
      StatusCode::setServerStatusCode(StatusCode::BAD_REQUEST);
      return;
    }

    $result = -1;
    $params = self::parsePostedParams();

    $phone = AccountController::parseAccessToken()[ContestAttribute::Phone->value];
    $beginningdate = Time::DateYMD("-", $params[ContestAttribute::Beginningdate->value]);
    $deadline = Time::DateYMD("-", $params[ContestAttribute::Deadline->value]);

    $params[ContestAttribute::Phone->value]         = $phone;
    $params[ContestAttribute::Beginningdate->value] = $beginningdate;
    $params[ContestAttribute::Deadline->value]      = $deadline;
    $params[ContestAttribute::Intramural->value]    =
      $params[ContestAttribute::Intramural->value] ? 1 : 0;

    $latestCode = ContestModel::getLatestContestCode();
    if ($latestCode == -1) {
      $result = 3;      // DB 오류 발생했으면 3
    }
    $params[ContestAttribute::Code->value] = $latestCode + 1;

    if ($result != 3) {
      $result = ContestModel::createContest($params) ? 1 : 3;
    }

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_JSON->value);
    printf("%s",
      (new JSON)->encode([ "status" => $result, "code" => $latestCode + 1 ])
    );

  }

  // 공모전 수정 요청 처리
  public static function handleUpdate(): void {

    if (!AccountController::signedIn()) {     // 로그인하지 않은 상태면 Bad Request 응답
      StatusCode::setServerStatusCode(StatusCode::BAD_REQUEST);
      return;
    }

    $result = -1;
    $params = self::parsePostedParams();
    $code   = $params[ContestAttribute::Code->value];
    $phone  = AccountController::parseAccessToken()[ContestAttribute::Phone->value];

    $deadline = Time::DateYMD("-", $params[ContestAttribute::Deadline->value]);
    $params[ContestAttribute::Deadline->value] = $deadline;

    if (ContestModel::isCreatedBy($code, $phone)) {
      $result = ContestModel::updateContest($params) ? 1 : 3;   // 수정했으면 1, DB 오류 발생했으면 3
    } else {
      $result = 2;    // 본인이 등록한 공모전이 아니면 2
    }

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_JSON->value);
    printf("%s",
      (new JSON)->encode([ "status" => $result ])
    );

  }

  // 공모전 모집종료 요청 처리
  public static function handleClose(): void {

    if (!AccountController::signedIn()) {     // 로그인하지 않은 상태면 Bad Request 응답
      StatusCode::setServerStatusCode(StatusCode::BAD_REQUEST);
      return;
    }

    $result = -1;
    $code = self::parsePostedParams()[ContestAttribute::Code->value];
    $phone = AccountController::parseAccessToken()[ContestAttribute::Phone->value];

    if (ContestModel::isCreatedBy($code, $phone)) {
      // 종료했으면 1, DB 오류 발생했으면 3
      $result = ContestModel::closeContest($code) ? 1 : 3;
    } else {
      $result = 2;    // 본인이 등록한 공모전이 아니면 2
    }

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_JSON->value);
    printf("%s",
      (new JSON)->encode([ "status" => $result ])
    );


  }

  // 공모전 삭제 요청 처리
  public static function handleDelete(): void {

    if (!AccountController::signedIn()) {     // 로그인하지 않은 상태면 Bad Request 응답
      StatusCode::setServerStatusCode(StatusCode::BAD_REQUEST);
      return;
    }

    $result = -1;   // 응답 결과
    $code = self::parsePostedParams()[ContestAttribute::Code->value];
    $phone = AccountController::parseAccessToken()[ContestAttribute::Phone->value];

    if (ContestModel::isCreatedBy($code, $phone)) {
      // 삭제했으면 1, DB 오류 발생했으면 3
      $result = ContestModel::deleteContest($code) ? 1 : 3;
    } else {
      $result = 2;    // 본인이 등록한 공모전이 아니면 2
    }

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_JSON->value);
    printf("%s",
      (new JSON)->encode([ "status" => $result ])
    );

  }

  // 공모전 등록 페이지 출력
  public static function viewCreate(): void {

    if (!AccountController::signedIn()) {
      self::redirectToSignin(); return;
    }

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);

    $page = ContestCreatePage::page(
      CsvDataLoader::loadMajorsList(),
      CsvDataLoader::loadRegionsList()
    );
    printf("%s", $page);

  }

  // 공모전 정보 출력
  public static function viewContestDetail(int $contestCode): void {

    if (!AccountController::signedIn()) {
      self::redirectToSignin(); return;
    }

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);

    $contestInfo = ContestModel::getContestInfoByCode($contestCode);  // 공모전 정보
    if (count($contestInfo) == 0) {           // 코드와 일치하는 공모전이 없는 경우
      StatusCode::setServerStatusCode(StatusCode::NOT_FOUND);
      printf("%s", ErrorPage::NotFound());
      return;
    }

    self::autoCloseContest($contestCode, $contestInfo);

    $phone = AccountController::parseAccessToken()[ContestAttribute::Phone->value];
    $page = ContestInfoPage::page($contestInfo,
      AccountModel::getUserInfoByPhone($phone),
      AccountModel::getUserInfoByPhone($contestInfo[AccountAttribute::Phone->value]),
      CsvDataLoader::loadMajorsList(),
      CsvDataLoader::loadRegionsList(),
      CsvDataLoader::loadCollegesList(),
      ContestModel::isCreatedBy($contestCode, $phone),
      ContestModel::appliedInContest($contestCode, $phone)
    );
    printf("%s", $page);

  }

  // 공모전 목록 출력
  public static function viewContestList(): void {

    if (!AccountController::signedIn()) {
      self::redirectToSignin(); return;
    }

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);

    $_GET["done"]   ??= -1;
    $_GET["major"]  ??= -1;
    $_GET["region"] ??= -1;

    $filters = [];
    if ($_GET["done"] != -1) {
      $filters["done"] = $_GET["done"];
    }
    if ($_GET["major"] != -1) {
      $filters["field"] = $_GET["major"];
    }
    if ($_GET["region"] != -1) {
      $filters["region"] = $_GET["region"];
    }

    $sortBy = (String)($_GET["sortby"] ?? "code");
    $ascending = (int)($_GET["asc"] ?? 0);

    $page = ContestListPage::page(
      CsvDataLoader::loadMajorsList(),
      CsvDataLoader::loadRegionsList(),
      ContestModel::getContests($filters, $sortBy, $ascending)
    );
    printf("%s", $page);

  }

  // 모집 기한이 지난 공모전은 Done=true로 변경한다.
  public static function autoCloseContest(int $code, Array $contestInfo = []): void {

    if (count($contestInfo) == 0) {
      $contestInfo = ContestModel::getContestInfoByCode($code);
    }
    $deadline = sprintf("%s 00:00:00", $contestInfo[ContestAttribute::Deadline->value]);
    $deadline = Time::StrTimestampToInt($deadline);
    if ($deadline <= time()) {
      ContestModel::closeContest($code);
    }

  }

};
