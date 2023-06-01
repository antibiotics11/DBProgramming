<?php

namespace ContestApp\Model;
use \ContestApp\Database\PdoConnector;

/**
 * 공모전 업무 처리 모델
 */
class ContestModel {

  private const TABLE_NAME_CONTEST   = "contest";
  private const TABLE_NAME_APPLICANT = "participant";
  private const SQL_SELECT_CONTEST   = "SELECT * FROM " . self::TABLE_NAME_CONTEST;
  private const SQL_SELECT_APPLICANT = "SELECT * FROM " . self::TABLE_NAME_APPLICANT;

  public static function getSelectQuery(Array $filters = [], String $sortBy = "code", bool $ascending = false): String {

    $where = "1=1";
    foreach ($filters as $columnName => $columnValue) {
      $where = sprintf("%s AND %s='%s'", $where, $columnName, $columnValue);
    }

    $query = sprintf("%s WHERE %s ORDER BY %s %s",
      self::SQL_SELECT_CONTEST,
      $where,
      $sortBy,
      ($ascending) ? "ASC" : "DESC"
    );

    return $query;
  }

  /**
   * 전체 공모전 목록을 가져온다.
   *
   * @param String $sortBy 정렬 기준이 될 컬럼명
   * @param bool $ascending 오름차순/내림차순
   * @return Array 공모전 목록, 또는 오류 발생한 경우 빈 배열
   */
  public static function getContests(Array $filters = [], String $sortBy = "code", bool $ascending = false): Array {

    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);
    $result = [];
    try {
      $stmt = $pdo->pdo->prepare(self::getSelectQuery($filters, $sortBy, $ascending));
      $stmt->execute();
      $result = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?? [];
    } catch (\PDOException $e) {
      throw $e;
    }

    return $result;

  }

  // 속성 => 값 과 일치하는 공모전 정보를 모두 가져온다.
  public static function getContestsInfo(String $attribute, String $value): Array {

    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);
    $result = $pdo->read(self::TABLE_NAME_CONTEST, [ $attribute, $value ]);

    return $result;

  }

  // 공모전 코드로 공모전 정보를 가져온다.
  public static function getContestInfoByCode(int $code): Array {

    $contestsInfo = self::getContestsInfo(ContestAttribute::Code->value, $code);
    if (count($contestsInfo) == 0) {
      return [];
    }
    return $contestsInfo[0];

  }

  // 게시자 휴대폰 번호로 공모전 정보를 가져온다.
  public static function getContestsInfoByPhone(String $phone): Array {
    return self::getContestsInfo(ContestAttribute::Phone->value, $phone);
  }

  // 가장 최근 게시된 공모전 정보를 가져온다.
  public static function getLatestContestInfo(): Array {
    return self::getContests()[0] ?? [];
  }

  // 가장 최근 게시된 공모전의 코드를 가져온다.
  public static function getLatestContestCode(): int {

    $contestInfo = self::getLatestContestInfo();
    return (count($contestInfo)) ? (int)$contestInfo[ContestAttribute::Code->value] : -1;

  }

  // 해당 공모전을 등록한 사람인지 확인한다.
  public static function isCreatedBy(int $code, String $phone): bool {

    $contestInfo = self::getContestInfoByCode($code);
    return (strcmp(
      trim($phone),
      trim($contestInfo[ContestAttribute::Phone->value])
    ) == 0) ? true : false;

  }

  // 모집 종료된 공모전인지 확인한다.
  public static function isClosed(int $code): bool {

    $contestInfo = self::getContestInfoByCode($code);
    return (bool)$contestInfo[ContestAttribute::Done->value];

  }

  // 새로운 공모전을 생성한다.
  public static function createContest(Array $contestInfo): bool {

    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);
    return $pdo->insert(self::TABLE_NAME_CONTEST, [
      $contestInfo[ContestAttribute::Code->value],
      $contestInfo[ContestAttribute::Phone->value],
      $contestInfo[ContestAttribute::Title->value],
      $contestInfo[ContestAttribute::Field->value],
      $contestInfo[ContestAttribute::Headcount->value],
      $contestInfo[ContestAttribute::Deadline->value],
      $contestInfo[ContestAttribute::Beginningdate->value],
      0,
      $contestInfo[ContestAttribute::Intramural->value],
      $contestInfo[ContestAttribute::Rating->value],
      $contestInfo[ContestAttribute::Region->value]
    ]);

  }

  // 공모전 정보를 수정한다.
  public static function updateContest(Array $contestInfo): bool {

    $code         = $contestInfo[ContestAttribute::Code->value];
    $newHeadcount = (int)$contestInfo[ContestAttribute::Headcount->value];
    $newDeadline  = (String)$contestInfo[ContestAttribute::Deadline->value];
    $newRegion    = (int)$contestInfo[ContestAttribute::Region->value];
    $newRating    = (int)$contestInfo[ContestAttribute::Rating->value];

    $query = sprintf("UPDATE %s SET %s=%d, %s='%s', %s=%d, %s=%d WHERE %s=%d",
      self::TABLE_NAME_CONTEST,
      ContestAttribute::Headcount->value, $newHeadcount,
      ContestAttribute::Deadline->value,  $newDeadline,
      ContestAttribute::Region->value,    $newRegion,
      ContestAttribute::Rating->value,    $newRating,
      ContestAttribute::Code->value,      $code
    );

    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);

    return (bool)$pdo->submit($query);

  }

  // 공모전 모집을 종료한다.
  public static function closeContest(int $code): bool {

    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);
    $query = sprintf("UPDATE %s SET done=1 WHERE code=%d", self::TABLE_NAME_CONTEST, $code);
    return (bool)$pdo->submit($query);

  }

  // 공모전을 삭제한다.
  public static function deleteContest(int $code): bool {

    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);
    return $pdo->delete(self::TABLE_NAME_CONTEST, [
      ContestAttribute::Code->value,
      (String)$code
    ]);

  }

  // 참가자 테이블의 전체 값을 가져온다.
  public static function getApplicants(int $code = -1, String $phone = ""): Array {

    $query = self::SQL_SELECT_APPLICANT;
    if ($code > 0) {
      $query = sprintf("%s WHERE %s=%d", $query, ContestAttribute::Code->value, $code);
    }
    if (strlen($phone) > 0) {
      $phone = sprintf("%s AND %s='%s'", $query, ContestAttribute::Phone->value, $phone);
    }

    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);
    $result = [];
    try {
      $stmt = $pdo->pdo->prepare($query);
      $stmt->execute();
      $result = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?? [];
    } catch (\PDOException $e) {
      throw $e;
    }

    return $result;

  }

  // 해당 공모전에 이미 참가했는지 확인한다.
  public static function appliedInContest(int $code, String $phone): bool {
    return (bool)count(self::getApplicants($code, $phone));
  }

  // 공모전에 참가신청한다.
  public static function applyInContest(int $code, String $phone): bool {

    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);
    return $pdo->insert(self::TABLE_NAME_APPLICANT, [ $phone, $code ]);

  }

  // 공모전 참가를 취소한다.
  public static function cancleApplication(int $code, String $phone): bool {

    $query = sprintf("DELETE FROM %s WHERE code=%d AND phone='%s'", self::TABLE_NAME_APPLICANT, $code, $phone);
    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);
    return $pdo->submit($query);

  }

};
