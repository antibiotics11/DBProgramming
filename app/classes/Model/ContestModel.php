<?php

namespace ContestApp\Model;
use \ContestApp\Database\PdoConnector;

/**
 * 공모전 업무 처리 모델
 */
class ContestModel {

  private const COLUMN_NAME_CONTEST     = "contest";
  private const SQL_SELECT_CONTEST      = "SELECT * FROM " . self::COLUMN_NAME_CONTEST;
  private const SQL_SELECT_CONTEST_SORT = self::SQL_SELECT_CONTEST . " ORDER BY %s %s";

  /**
   * 전체 공모전 목록을 가져온다.
   *
   * @param String $sortBy 정렬 기준이 될 컬럼명
   * @param bool $ascending 오름차순/내림차순
   * @return Array 공모전 목록, 또는 오류 발생한 경우 빈 배열
   */
  public static function getContests(String $sortBy = "code", bool $ascending = false): Array {

    $rank = ($ascending) ? "ASC" : "DESC";
    $query = sprintf(self::SQL_SELECT_CONTEST_SORT, $sortBy, $rank);

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

  // 속성 => 값 과 일치하는 공모전 정보를 모두 가져온다.
  public static function getContestsInfo(String $attribute, String $value): Array {

    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);
    $result = $pdo->read(self::COLUMN_NAME_MEMBER, [ $attribute, $value ]);

    return $result;

  }

  // 가장 마지막으로 생성된 공모전의 코드를 가져온다.
  public static function getLatestContestCode(): int {

    try {
      $contests = self::getContests();
      return (count($contests)) ? (int)$contests[0][ContestAttribute::Code->value] : 0;
    } catch (\PDOException) {
      return -1;
    }

  }

  // 새로운 공모전을 생성한다.
  public static function createContest(Array $contestInfo): bool {

    $pdo = new PdoConnector(\MYSQL_HOSTNAME, \MYSQL_DBNAME, \MYSQL_USERNAME, \MYSQL_PASSWORD);
    return $pdo->insert(self::COLUMN_NAME_CONTEST, [
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

};
