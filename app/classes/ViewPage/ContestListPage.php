<?php

namespace ContestApp\ViewPage;

class ContestListPage {

  private const VIEW_CONTEST_LIST_FORM_PATH = \APP_VIEW_PATH . "/contestlist.html";
  private static function loadContestsList(Array $contestsList): String {

    $list = "";
    for ($c = 0; $c < count($contestsList); $c++) {

    }

    return $list;

  }

  private const VIEW_CONTEST_LIST_PATH = \APP_VIEW_PATH . "/contestlistpage.html";
  public static function page(Array $contestsList): String {
    return ViewPage::assemble(
			sprintf(ViewPage::read(self::VIEW_CONTEST_LIST_PATH),
				self::loadContestsList($contestsList)
			)
		);
  }

};
