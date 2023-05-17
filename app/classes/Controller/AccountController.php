<?php

namespace ContestApp\Controller;

use \CommandString\Cookies\Cookie;
use \aalfiann\JSON;
use \ContestApp\Http\{Header, StatusCode};
use \ContestApp\Resource\MimeType;
use \ContestApp\Security\WebToken;
use \ContestApp\Model\AccountModel;
use \ContestApp\ViewPage\{SigninPage, SignupPage, AccountInfoPage};

/**
 * 계정 관련 요청 컨트롤러
 */
class AccountController {

  public static function setAccessToken(String $accessToken): void {
    (new Cookie)->set("AccessToken", $accessToken, 2, 0, 0, "/", \SERVICE_URL, true, true);
  }

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
    return isset($tokenValues["phone"]);

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

  // POST된 파라미터 중 AccountController에서 관리할 값들을 가져온다.
  public static function parsePostedParams(): Array {

    foreach ($_POST as $name => $value) {
      $_POST[$name] = base64_decode($value);
    }

    $phone    = $_POST["phone"] ?? "";
    $password = $_POST["password"] ?? "";
    $name     = $_POST["name"] ?? "";
    $college  = $_POST["college"] ?? "";
    $sex      = $_POST["sex"] ?? "0";
    $email    = $_POST["email"] ?? "";
    $major    = $_POST["major"] ?? "";
    $birthday = $_POST["birthday"] ?? "2000-01-01";

    return [
      "phone"    => (String)trim($phone),
      "password" => (String)trim($password),
      "name"     => (String)trim($name),
      "college"  => (String)trim($college),
      "sex"      => (bool)trim($sex),
      "email"    => (String)trim($email),
      "major"    => (String)trim($major),
      "birthday" => (String)trim($birthday)
    ];

  }

  // 로그인 여부에 따라 리디렉션
  public static function redirect(): void {

    $location = self::signedIn() ? "/account/info" : "/account/signin";

    StatusCode::setServerStatusCode(StatusCode::MOVED_TEMPORARILY);
    Header::setServerHeader(Header::LOCATION, $location);

  }

  // 로그인 페이지 조회
  public static function viewSignin(): void {

    if (self::signedIn()) {    // 로그인한 사용자면 리디렉션
      self::redirect();
    }

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);
    printf("%s", SigninPage::page());

  }

  // 회원가입 페이지 조회
  public static function viewSignup(): void {

    if (self::signedIn()) {    // 로그인한 사용자면 리디렉션
      self::redirect();
    }

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);
    printf("%s", SignupPage::page());

  }

  // 계정정보 조회
  public static function viewAccountInfo(): void {

    if (!self::signedIn()) {   // 로그인하지 않은 사용자면
      self::redirect();
    }

    $phone = self::parseAccessToken()["phone"];
    $accountInfo = AccountModel::getUserInfoByPhone($phone);

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);
    printf("%s", AccountInfoPage::page($accountInfo));

  }

  // 로그인 요청 처리
  public static function handleSignin(): void {

    if (self::signedIn()) {    // 로그인한 사용자면
      StatusCode::setServerStatusCode(StatusCode::BAD_REQUEST);
      return;
    }

    $result   = -1;
    $params   = self::parsePostedParams();
    $phone    = $params["phone"];
    $password = $params["password"];

    if (count(AccountModel::getUserInfoByPhone($params["phone"])) == 0) {
      $result = 3;
    } else {
      $result = 2;
      if (AccountModel::passwordMatches($phone, $password)) {
        self::setAccessToken(WebToken::create([ "phone" => $phone ]));
        $result = 1;
      }
    }

    $encoded = (new JSON)->encode([ "status" => $result ]);

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_JSON->value);
    printf("%s", $encoded);

  }

  // 회원가입 요청 처리
  public static function handleSignup(): void {

    if (self::signedIn()) {    // 로그인한 사용자면
      StatusCode::setServerStatusCode(StatusCode::BAD_REQUEST);
      return;
    }

    $result = -1;
    $params = self::parsePostedParams();

    foreach ($params as $value) {
      if (strcmp(gettype($value), "string") !== 0) {
        continue;
      }
      if (strlen($value) == 0) {
        $result = 3;
      }
    }

    if ($result != 3) {
      if (count(AccountModel::getUserInfoByPhone($params["phone"])) != 0) {
        $result = 2;
      } else {
        $result = (AccountModel::createNewAccount($params)) ? 1 : 4;
      }
    }

    $encoded = (new JSON)->encode([ "status" => $result ]);

    Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_JSON->value);
    printf("%s", $encoded);

  }

  // 로그아웃 요청 처리
  public static function handleSignout(): void {

    if (!self::signedIn()) {   // 로그인하지 않은 사용자면
      return;
    }

    $accessToken = WebToken::expire(self::getAccessToken());
    self::setAccessToken($accessToken);

    self::redirect();

  }

};
