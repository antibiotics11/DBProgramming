<?php

namespace ContestApp\ViewPage;
use \ContestApp\Model\ContestAttribute;

class ContestListPage {

  private static function loadRatingMessage(int $ratingCondition): String {
    return match ($ratingCondition) {
      0 => "무관", 1 => "좋음", 2 => "보통 이상", 3 => "나쁨 이상"
    };
  }

  private static function loadStatusMessage(int $done): Array {
    return [ "message" => $done ? "모집완료" : "모집중", "color" => $done ? "grey" : "green" ];
  }

  private const VIEW_CONTEST_LIST_FORM_PATH = \APP_VIEW_PATH . "/contestlist.html";
  private static function loadContestsList(Array $contestsList, Array $majorsList): String {

    $majors = [];
    for ($m = 0; $m < count($majorsList); $m++) {
      $majors[(int)$majorsList[$m]["code"]] = $majorsList[$m]["name"];
    }

    $list = "";
    for ($c = 0; $c < count($contestsList); $c++) {

      $status = self::loadStatusMessage($contestsList[$c][ContestAttribute::Done->value]);
      $contestInfo = sprintf(ViewPage::read(self::VIEW_CONTEST_LIST_FORM_PATH),
        $contestsList[$c][ContestAttribute::Code->value],
        $contestsList[$c][ContestAttribute::Title->value],
        $majors[(int)$contestsList[$c][ContestAttribute::Field->value]],
        $contestsList[$c][ContestAttribute::Beginningdate->value],
        $contestsList[$c][ContestAttribute::Deadline->value],
        self::loadRatingMessage($contestsList[$c][ContestAttribute::Rating->value]),
        $status["color"], $status["message"]
      );
      $list = sprintf("%s\r\n%s", $list, $contestInfo);
    }
    if (strlen($list) == 0) {
      $list = "<h2 style=\"color:orange;margin-top:5em;\">일치하는 정보가 없습니다.</h2>";
    }

    return $list;

  }

  private const VIEW_CONTEST_LIST_PATH = \APP_VIEW_PATH . "/contestlistpage.html";
  public static function page(Array $majorsList, Array $regionsList, Array $contestsList): String {
    return ViewPage::assemble(
      sprintf(ViewPage::read(self::VIEW_CONTEST_LIST_PATH),
        ViewPage::loadSelectOptions($majorsList),
        ViewPage::loadSelectOptions($regionsList),
        self::loadContestsList($contestsList, $majorsList)
      )
    );
  }

};
