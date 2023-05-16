<?php

namespace ContestApp\Session;

use Firebase\JWT\{JWT, Key, ExpiredException};

class WebToken {

  private const SECRET_KEY  = "FWU2CKRP7DZ87R7DUJLBJJRUYHVEMWBXE5VADE3PW26QY9XNXPHXBHBWEDVD4VSN";
  private const EXPIRE_TIME = 60 * 60 * 2;

  /**
   * 새로운 토큰을 생성한다.
   *
   * @param  Array  $data  페이로드에 담을 데이터
   * @return  String  생성된 토큰
   */
  public static function create(Array $data): String {

    $timestamp = time();
    $payload = [
      "iat"  => $timestamp,
      "exp"  => $timestamp + self::EXPIRE_TIME
    ];

    foreach ($data as $index => $value) {
      $payload[$index] = $value;
    }

    $token = JWT::encode($payload, self::SECRET_KEY, "HS256");

    return $token;

  }

  /**
   * 토큰에서 페이로드를 읽어 배열로 반환하거나, 예외 발생시 빈 배열을 반환한다.
   *
   * @param  String  $token  토큰
   * @return  Array  페이로드
   */
  public static function read(String $token): Array {

    $payload = [];
    try {
      $payload = JWT::decode($token, new Key(self::SECRET_KEY, "HS256"));
      $payload = (Array)$payload;
    } catch (\Throwable $e) {
      return $payload;
    }

    return $payload;

  }

  /**
   * 토큰의 유효시간을 앞당겨 강제로 만료시킨다.
   *
   * @param  String  $token  만료시킬 토큰
   * @return  String  만료된 토큰
   */
  public static function expire(String $token): String {

    $payload = self::read($token);
    $payload["exp"] = time();

    $token = JWT::encode($payload, self::SECRET_KEY, "HS256");

    return $token;

  }

  public static function isExpired(String $token): bool {

    try {
      JWT::decode($token, new Key(self::SECRET_KEY, "HS256"));
    } catch (ExpiredException $e) {
      return true;
    }

    return false;

  }

};
