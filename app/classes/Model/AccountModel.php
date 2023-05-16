<?php

namespace ContestApp\Model;
use \ContestApp\Database\PdoConnector;

/**
 * 계정 관련 작업 처리 모델
 */
class AccountModel {

  private const COLUMN_NAME_MEMBER = "member";

  public static function getUserInfo(String $attribute, String $value): Array {

    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);
    $result = $pdo->read(self::COLUMN_NAME_MEMBER, [ $attribute, $value ]);

    return $result[0] ?? [];

  }

  public static function getUserInfoByPhone(String $phone): Array {
    return self::getUserInfo("phone", $phone);
  }

  public static function getPasswordHash(String $password): String {
    return password_hash($password, \PASSWORD_DEFAULT);
  }

  public static function passwordMatches(String $phone, String $password): bool {
    return password_verify($password, self::getUserInfoByPhone($phone)["password"]);
  }

  public static function createNewAccount(Array $accountInfo): bool {

    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);
    return $pdo->insert(self::COLUMN_NAME_MEMBER, [
      $accountInfo["phone"],
      self::getPasswordHash($accountInfo["password"]),
      $accountInfo["name"],
      $accountInfo["college"],
      ($accountInfo["sex"] ? 1 : 0),
      $accountInfo["email"],
      $accountInfo["major"],
      $accountInfo["birthday"],
      -1
    ]);

  }

};
