<?php

namespace ContestApp\Database;
use \PDO, \PDOException;

/**
 * MySQL DB 접속을 위한 PDO 객체와 관련 메소드를 정의 
 */
class PdoConnector {

	private ?PDO    $pdo;
	private String  $pdoExceptionMessage;

	private const PDO_OPTIONS = [
		PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_EMULATE_PREPARES   => false
	];

	public function __construct() {

		$this->pdo = new PDO(
			sprintf("mysql:host=%s;dbname=%s;charset=utf8", \MYSQL_HOSTNAME, \MYSQL_DBNAME),
			MYSQL_USERNAME,
			MYSQL_PASSWORD,
			self::PDO_OPTIONS
		);
		$this->pdoExceptionMessage = "";

	}

	public function __destruct() {
		$this->pdo = null;
	}

	public function getPdoExceptionMessage(): String {
		return $this->pdoExceptionMessage;
	}

	public function insert(String $tableName, Array $values): int {

		$params = "";	
		for ($i = 0; $i < count($values); $i++) {
			$value = (strcmp(gettype($values[$i]), "integer") == 0) ?
				$values[$i] : sprintf("'%s'", $values[$i]);
			$params = sprintf("%s%s %s", $params, (($i > 0) ? "," : ""), $value);
		}

		$query = sprintf("INSERT INTO %s VALUES(%s);", $tableName, $params);
		return $this->submit($query);

	}

	public function submit(String $query): bool {
	
		try {
			$stmt = $this->pdo->prepare(trim($query));
			$stmt->execute();
			return true;
		} catch (PDOException $e) {
			$this->pdoExceptionMessage = $e->getMessage();
			return false;
		}

	}

	public function read(String $tableName, Array $conditions): Array {

		$query = sprintf("SELECT * FROM %s WHERE %s='%s';", $tableName, 


	public function delete(String $tableName, Array $conditions): int {

		$query = sprintf("DELETE FROM %s", $tableName);
		$conditionsCount = count($conditions);
		
		if ($conditionsCount > 0) {
			$query = sprintf("%s WHERE", $query);
		}
		for ($i = 0; $i < $conditionsCount; $i++) {
			
		
			$query = sprintf("%s %s='%s'", $query, $conditions[$i][0], $conditions[$i][1]);
			if ($i != $conditionsCount - 1) {
				$query = sprintf("%s AND "
			}
		}

	}

  public function read(String $table, Array $conditions): Array {

    $query = sprintf("SELECT * FROM %s WHERE %s='%s';", $table, $conditions[0], $conditions[1]);

    try {
      $stmt = $this->pdo->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      return [];
    }

  }

  public function delete(String $table, Array $conditions): int {

    $query = sprintf("DELETE FROM %s WHERE %s='%s';", $table, $conditions[0], $conditions[1]);

    return $this->submit($query);

  }
};
