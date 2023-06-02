<?php

namespace ContestApp\Controller;

use \CommandString\Cookies\Cookie;
use \aalfiann\JSON;

use \ContestApp\Http\{Header, StatusCode};
use \ContestApp\Resource\{MimeType, CsvDataLoader};
use \ContestApp\Security\WebToken;
use \ContestApp\Model\{AccountAttribute, AccountModel};
use \ContestApp\ViewPage\{SigninPage, SignupPage, AccountInfoPage, ErrorPage};

/**
 * 계정 관련 요청 컨트롤러
 */
class AccountController {

  // AccessToken 쿠키를 설정한다.
  public static function setAccessToken(String $accessToken): void {
    (new Cookie)->set("AccessToken", $accessToken, 2, 0, 0, "/", \SERVICE_URL, true, true);
  }

  // AccessToken 쿠키 값을 반환하고, 쿠키가 없으면 빈 문자열을 반환한다.
  public static function getAccessToken(): String {
    try {
      return (new Cookie)->get("AccessToken");
    } catch (\Exception) {
      return "";
    }
  }

  // 이미 로그인한 사용자인지 확인한다.
  public static function signedIn(): bool {

    $tokenValues = self::parseAccessToken();
    if (count($tokenValues) == 0) {
      return false;
    }

    return isset($tokenValues[AccountAttribute::Phone->value]);

  }

  // 유효한 액세스 토큰에 저장된 정보를 가져온다.
  public static function parseAccessToken(): Array {

    $accessToken = self::getAccessToken();
    if (strlen($accessToken) < 100) {          // 토큰이 없으면
      return [];
    }
    if (WebToken::isExpired($accessToken)) {   // 만료된 토큰이면
      return [];
    }

    return WebToken::read($accessToken);

  }

  // POST된 파라미터 중 AccountController에서 관리할 속성 값들을 정리해서 가져온다.
  public static function parsePostedParams(): Array {

    $decoded = [];
    foreach ($_POST as $name => $value) {
      $decoded[strtolower(trim($name))] = urldecode($value);
    }
    foreach (AccountAttribute::cases() as $attribute) {
      $attribute = $attribute->value;
      $decoded[$attribute] = AccountAttribute::trimValue($attribute, $decoded[$attribute] ?? "");
    }

    return $decoded;

  }

  // 로그인한 상태면 계정정보 페이지, 로그인하지 않았으면 로그인 페이지로 리디렉션
  public static function redirect(): void {

    $location = self::signedIn() ? "/account/info" : "/account/signin";

    StatusCode::setServerStatusCode(StatusCode::MOVED_TEMPORARILY);
    Header::setServerHeader(Header::LOCATION, $location);

  }

  // 로그인 페이지 출력
  public static function viewSignin(): void {

    if (self::signedIn()) {    // 로그인한 사용자면 리디렉션
      self::redirect(); return;
    }

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);
    printf("%s", SigninPage::page());

  }

  // 회원가입 페이지 출력
  public static function viewSignup(): void {

    if (self::signedIn()) {    // 로그인한 사용자면 리디렉션
      self::redirect(); return;
    }

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);

    $collegesList = CsvDataLoader::loadCollegesList();
    $majorsList = CsvDataLoader::loadMajorsList();
    printf("%s", SignupPage::page($collegesList, $majorsList));

  }

  // 계정정보 출력
  public static function viewAccountInfo(): void {

    if (!self::signedIn()) {   // 로그인하지 않은 사용자면 리디렉션
      self::redirect(); return;
    }

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);

    $phone = self::parseAccessToken()[AccountAttribute::Phone->value];
    $accountInfo = AccountModel::getUserInfoByPhone($phone);   // 계정정보를 받아온다

    $collegesList = CsvDataLoader::loadCollegesList();
    $majorsList = CsvDataLoader::loadMajorsList();

    printf("%s", AccountInfoPage::page($accountInfo, $collegesList, $majorsList));

  }

  // 다른회원 계정정보 출력
  public static function viewMemberInfo(String $memberPhone): void {

    if (!self::signedIn()) {   // 로그인하지 않은 사용자면 리디렉션
      self::redirect(); return;
    }

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);

    $memberInfo = AccountModel::getUserInfoByPhone($memberPhone);
    $collegesList = CsvDataLoader::loadCollegesList();
    $majorsList = CsvDataLoader::loadMajorsList();

    printf("%s", AccountInfoPage::page($memberInfo, $collegesList, $majorsList));

  }

  // 로그인 요청 처리
  public static function handleSignin(): void {

    if (self::signedIn()) {    // 로그인한 사용자면 Bad Request 응답
      StatusCode::setServerStatusCode(StatusCode::BAD_REQUEST);
      return;
    }

    $params   = self::parsePostedParams();
    $phone    = $params[AccountAttribute::Phone->value];
    $password = $params[AccountAttribute::Password->value];

    $result   = -1;
    if (!count(AccountModel::getUserInfoByPhone($phone))) {
      $result = 3;        // 휴대폰 번호와 일치하는 계정이 없으면 3
    } else {
      $result = 2;        // 패스워드가 일치하지 않으면 2
      if (AccountModel::passwordMatches($phone, $password)) {

        self::setAccessToken(WebToken::create([ AccountAttribute::Phone->value => $phone ]));
        (new Cookie)->set(AccountAttribute::Phone->value, $phone);   // AccessToken 쿠키를 생성
        $result = 1;      // 패스워드가 일치하면 1 (로그인 성공)

      }
    }

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_JSON->value);
    printf("%s", (new JSON)->encode([ "status" => $result ]));

  }

  // 회원가입 요청 처리
  public static function handleSignup(): void {

    if (self::signedIn()) {    // 로그인한 사용자면
      StatusCode::setServerStatusCode(StatusCode::BAD_REQUEST);
      return;
    }

    $result = -1;

    $params = self::parsePostedParams();
    $params[AccountAttribute::Rating->value] = -1;
    foreach (AccountAttribute::cases() as $attribute) {
      $paramValue = $params[$attribute->value];
      if (strcmp(gettype($paramValue), "string") !== 0) {
        continue;        // 타입이 문자열이 아닌 속성 값은 일단 패스
      }
      if (strlen($paramValue) < 2) {
        $result = 3;     // 비어있는 속성 값이 하나라도 있으면
        break;
      }
    }

    if ($result != 3) {
      $phone = $params[AccountAttribute::Phone->value];
      if (count(AccountModel::getUserInfoByPhone($phone)) != 0) {
        $result = 2;     // 이미 같은 휴대폰 번호를 쓰는 계정이 있으면
      } else {
        // DB에 insert 성공했으면 1, 실패했으면 4
        $result = (AccountModel::createNewAccount($params)) ? 1 : 4;
      }
    }

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_JSON->value);
    printf("%s", (new JSON)->encode([ "status" => $result ]));

  }

  // 로그아웃 요청 처리
  public static function handleSignout(): void {

    if (!self::signedIn()) {   // 로그인하지 않은 사용자면
      StatusCode::setServerStatusCode(StatusCode::BAD_REQUEST);
      return;
    }

    $accessToken = WebToken::expire(self::getAccessToken());
    self::setAccessToken($accessToken);                      // AccessToken을 만료시킨다
    (new Cookie)->set(AccountAttribute::Phone->value, "");   // phone 쿠키를 제거한다

    self::redirect();  // 리디렉션

  }

  // 회원탈퇴 요청을 처리한다.
  public static function handleDelete(): void {

    if (!self::signedIn()) {   // 로그인하지 않은 사용자면
      StatusCode::setServerStatusCode(StatusCode::BAD_REQUEST);
      return;
    }

    $phone  = self::parseAccessToken()[AccountAttribute::Phone->value];
    $result = AccountModel::deleteAccount($phone);
    if ($result) {
      self::handleSignout();   // 회원탈퇴 후 자동 로그아웃
    } else {
      // DB 오류가 발생했으면 Internal Server Error 출력
      StatusCode::setServerStatusCode(StatusCode::INTERNAL_SERVER_ERROR);
      Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);
      printf("%s", ErrorPage::InternalServerError());
    }

  }

  // 평가 요청을 처리한다.
  public static function handleRate(String $targetPhone): void {

    if (!self::signedIn()) {   // 로그인하지 않은 사용자면
      StatusCode::setServerStatusCode(StatusCode::BAD_REQUEST);
      return;
    }

    $phoneParser = fn($phone) => preg_replace("/[^0-9]/", "", trim($phone));
    $targetPhone = $phoneParser($targetPhone);
    $memberPhone = $phoneParser(self::parseAccessToken()[AccountAttribute::Phone->value]);
    $like = self::parsePostedParams()["like"];

    $result = -1;
    if (count(AccountModel::getUserInfoByPhone($targetPhone)) &&
        count(AccountModel::getUserInfoByPhone($memberPhone))) {
      if (!count(AccountModel::getRatingByTargetAndMember($targetPhone, $memberPhone))) {
        if (strcmp($targetPhone, $memberPhone) != 0) {
          $result = AccountModel::rate($targetPhone, $memberPhone, (bool)$like) ? 1 : 4;
        } else {
          $result = 3;   // 자기 자신을 평가하려고 하면 3
        }
      } else {
        $result = 2;   // 이미 평가했으면 2
      }
    } else {
      $result = 3;   // 일치하는 계정이 없으면 3
    }

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_JSON->value);
    printf("%s", (new JSON)->encode([ "status" => $result ]));

  }

};
