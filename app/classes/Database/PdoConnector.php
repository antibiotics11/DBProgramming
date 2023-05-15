<?php

namespace ContestApp\Database;

use PDO, PDOException;

class PdoConnector {

  public ?PDO $pdo;

  private Array $options = [
    PDO::MYSQL_ATTR_INIT_COMMAND       => "SET NAMES utf8mb4",
    PDO::ATTR_ERRMODE                  => PDO::ERRMODE_EXCEPTION,      // 오류 발생시에는 PDOException 발생
    PDO::ATTR_EMULATE_PREPARES         => false,                       //
    PDO::ATTR_AUTOCOMMIT               => true,                        //
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,                        //
    PDO::ATTR_DEFAULT_FETCH_MODE       => PDO::FETCH_ASSOC             //
  ];

  public function __construct(String $host, String $dbname, String $username, String $password) {
    $this->init($host, $dbname, $username, $password);
  }

  public function init(String $host, String $dbname, String $username, String $password): bool {
    $dsn = sprintf("mysql:host=%s;dbname=%s;charset=utf8", $host, $dbname);
    try {
      $this->pdo = new PDO($dsn, $username, $password, $this->options);
      return ($this->pdo !== null);
    } catch (PDOException $e) {
      return false;
    }
  }

  public function __destruct() {
    $this->pdo = null;
  }

  public function insert(String $table, Array $values): int {

    $params = "";
    for ($i = 0; $i < count($values); $i++) {
      $value = (strcmp(gettype($values[$i]), "integer") == 0) ?
        $values[$i] : sprintf("'%s'", $values[$i]);
      $params = sprintf("%s%s %s", $params, (($i > 0) ? "," : ""), $value);
    }

    $query = sprintf("INSERT INTO %s VALUES(%s);", $table, $params);

    return $this->submit($query);

  }

  public function submit(String $query): int {

    try {
      $stmt = $this->pdo->prepare($query);
      $stmt->execute();
      return 1;
    } catch (PDOException $e) {
      return 0;
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
