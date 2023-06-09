<?php

namespace ContestApp\Model;
use \ContestApp\Database\PdoConnector;

/**
 * 계정 관련 작업 처리 모델
 */
class AccountModel {

  private const TABLE_NAME_MEMBER = "member";
  private const TABLE_NAME_RATING = "rating";

  // 속성 => 값 과 일치하는 계정들 정보를 모두 가져온다.
  public static function getUsersInfo(String $attribute, String $value): Array {

    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);
    $result = $pdo->read(self::TABLE_NAME_MEMBER, [ $attribute, $value ]);

    return $result;

  }

  // 속성 => 값 과 일치하는 계정들 중 첫번째 계정 정보를 가져온다.
  public static function getUserInfo(String $attribute, String $value): Array {
    return self::getUsersInfo($attribute, $value)[0] ?? [];
  }

  // phone 값이 일치하는 계정 정보를 가져온다.
  public static function getUserInfoByPhone(String $phone): Array {
    return self::getUserInfo(AccountAttribute::Phone->value, $phone);
  }

  // 패스워드를 해싱하여 반환한다.
  public static function getPasswordHash(String $password): String {
    return \password_hash($password, \PASSWORD_DEFAULT);
  }

  // 입력된 패스워드가 DB의 패스워드와 일치하는지 확인한다.
  public static function passwordMatches(String $phone, String $password): bool {
    return \password_verify(
      $password,
      self::getUserInfoByPhone($phone)[AccountAttribute::Password->value]
    );
  }

  // 새로운 계정을 생성한다.
  public static function createNewAccount(Array $accountInfo): bool {

    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);
    return $pdo->insert(self::TABLE_NAME_MEMBER, [
      $accountInfo[AccountAttribute::Phone->value],
      self::getPasswordHash($accountInfo[AccountAttribute::Password->value]),
      $accountInfo[AccountAttribute::Name->value],
      $accountInfo[AccountAttribute::College->value],
      ($accountInfo[AccountAttribute::Sex->value] ? 1 : 0),
      $accountInfo[AccountAttribute::Email->value],
      $accountInfo[AccountAttribute::Major->value],
      $accountInfo[AccountAttribute::Birthday->value],
      -1
    ]);

  }

  // 계정을 삭제한다.
  public static function deleteAccount(String $phone): bool {

    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);
    return $pdo->delete(self::TABLE_NAME_MEMBER, [ AccountAttribute::Phone->value, $phone ]);

  }

  // 회원을 평가한다.
  public static function rate(String $targetPhone, String $memberPhone, bool $like): bool {

    $ratingQuery = sprintf("UPDATE %s SET %s=%s%s1 WHERE %s='%s'",
      self::TABLE_NAME_MEMBER,
      AccountAttribute::Rating->value, AccountAttribute::Rating->value,
      ($like ? "+" : "-"),
      AccountAttribute::Phone->value, $targetPhone
    );

    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);
    return (
      $pdo->insert(self::TABLE_NAME_RATING, [ $targetPhone, $memberPhone, (int)$like ])
      && (bool)$pdo->submit($ratingQuery)
    );

  }

  // 속성 => 값과 일치하는 평가 내역을 가져온다.
  public static function getRating(String $attribute, String $value): Array {

    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);
    $result = $pdo->read(self::TABLE_NAME_RATING, [ $attribute, $value ]);

    return $result;

  }

  public static function getRatingByTarget(String $phone): Array {
    return self::getRating("target", $phone);
  }

  public static function getRatingByMember(String $phone): Array {
    return self::getRating("phone", $phone);
  }

  public static function getRatingByTargetAndMember(String $targetPhone, String $memberPhone): Array {

    $query = sprintf("SELECT * FROM %s WHERE target='%s' AND phone='%s'",
      self::TABLE_NAME_RATING,
      $targetPhone,
      $memberPhone
    );
    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);

    try {
      $stmt = $pdo->pdo->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
      return [];
    }

  }

};
