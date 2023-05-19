<?php

namespace ContestApp\Resource;

class CsvDataLoader {

  public static function loadCsvData(String $csvFilePath): Array {

    $csvParser = new \ParseCsv\Csv;
    $csvParser->auto($csvFilePath);
    return $csvParser->data;

  }

  // 전체 대학교 목록 파일에서 폐교되지 않은 대학만 학교명과 학교코드를 가져온다.
  public const CSV_PATH_COLLEGES_LIST = \APP_ASSETS_PATH . "/colleges_list.csv";
  public static function loadCollegesList(): Array {

    $csvData = self::loadCsvData(self::CSV_PATH_COLLEGES_LIST);

    $colleges = [];
    for ($c = 0; $c < count($csvData); $c++) {

      if (strcmp($csvData[$c]["학교구분"], "대학") !== 0) {
        continue;
      }
      if (strcmp($csvData[$c]["학교상태"], "기존") !== 0) {
        continue;
      }

      $colleges[$c]["name"] = (String)$csvData[$c]["학교명"];
      $colleges[$c]["code"] = (int)$csvData[$c]["학교코드"];

    }

    return $colleges;

  }

  // 전체 전공목록 파일에서 중계열 분류명과 분류코드를 가져온다.
  public const CSV_PATH_MAJORS_LIST = \APP_ASSETS_PATH . "/majors_list.csv";
  public static function loadMajorsList(): Array {

    $csvData = self::loadCsvData(self::CSV_PATH_MAJORS_LIST);

    $majors = [];
    $codes = [];
    for ($m = 0; $m < count($csvData); $m++) {

      $majorCode = (int)$csvData[$m]["중계열코드"];
      if (isset($codes[$majorCode])) {
        continue;
      }

      $codes[$majorCode] = true;
      $majors[] = [
        "name" => (String)$csvData[$m]["중계열"],
        "code" => $majorCode
      ];

    }

    return $majors;

  }

  // 지역명과 지역코드를 가져온다.
  public const CSV_PATH_REGIONS_LIST = \APP_ASSETS_PATH . "/regions_list.csv";
  public static function loadRegionsList(): Array {

    $csvData = self::loadCsvData(self::CSV_PATH_REGIONS_LIST);

    $regions = [];
    for ($r = 0; $r < count($csvData); $r++) {
      $regions[$r]["name"] = (String)$csvData[$r]["지역"];
      $regions[$r]["code"] = (int)$csvData[$r]["코드"];
    }

    return $regions;

  }

};
