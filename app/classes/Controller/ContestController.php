<?php

namespace ContestApp\Controller;

use \aalfiann\JSON;
use \ContestApp\Http\{Header, StatusCode};
use \ContestApp\Resource\{MimeType, CsvDataLoader};
use \ContestApp\Model\{ContestAttribute, ContestModel, AccountModel};
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

    if (count(ContestModel::getContestInfoByCode($code)) !== 0) {
      if (!ContestModel::isCreatedBy($code, $phone)) {

        $success = false;
        if (ContestModel::appliedInContest($code, $phone)) {
          $apply = 0;
          $success = ContestModel::cancleApplication($code, $phone);
        } else {
          $apply = 1;
          $success = ContestModel::applyInContest($code, $phone);
        }
        $result = $success ? 1 : 4;  // 처리 완료했으면 1, DB 오류 발생했으면 4

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

    if (ContestModel::isCreatedBy($code, $phone)) {
      // 수정했으면 1, DB 오류 발생했으면 3
      $result = ContestModel::updateContest($code, $params) ? 1 : 3;
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

    $phone = AccountController::parseAccessToken()[ContestAttribute::Phone->value];
    $page = ContestInfoPage::page($contestInfo,
      AccountModel::getUserInfoByPhone($phone),
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

};
