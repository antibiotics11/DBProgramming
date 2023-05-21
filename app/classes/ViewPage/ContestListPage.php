<?php

namespace ContestApp\ViewPage;
use \ContestApp\Model\ContestAttribute;

class ContestListPage {

  private const VIEW_CONTEST_LIST_FORM_PATH = \APP_VIEW_PATH . "/contestlist.html";
  private static function loadContestsList(Array $contestsList, Array $majorsList): String {

    $majors = [];
    for ($m = 0; $m < count($majorsList); $m++) {
      $majors[(int)$majorsList[$m]["code"]] = $majorsList[$m]["name"];
    }

    $list = "";
    for ($c = 0; $c < count($contestsList); $c++) {

      $contestInfo = sprintf(ViewPage::read(self::VIEW_CONTEST_LIST_FORM_PATH),
        $contestsList[$c][ContestAttribute::Code->value],
        $contestsList[$c][ContestAttribute::Title->value],
        $majors[(int)$contestsList[$c][ContestAttribute::Field->value]],
        $contestsList[$c][ContestAttribute::Beginningdate->value],
        $contestsList[$c][ContestAttribute::Deadline->value],
        $contestsList[$c][ContestAttribute::Rating->value],
        ($contestsList[$c][ContestAttribute::Done->value] ? "모집완료" : "모집중")
      );
      $list = sprintf("%s\r\n%s", $list, $contestInfo);
    }
    if (strlen($list) == 0) {
      $list = "<div>해당하는 정보가 없습니다.</div>";
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
