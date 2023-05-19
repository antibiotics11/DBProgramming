<?php

namespace ContestApp\ViewPage;

class ContestCreatePage {

  // 공모전 등록 페이지
  private const VIEW_CONTEST_CREATE_PATH = \APP_VIEW_PATH . "/add_contest.html";
  public static function page(Array $majorsList, Array $regionsList): String {
    return ViewPage::assemble(
      sprintf(ViewPage::read(self::VIEW_CONTEST_CREATE_PATH),
        ViewPage::loadSelectOptions($majorsList),
        ViewPage::loadSelectOptions($regionsList)
      )
    );
  }

};
