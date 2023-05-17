<?php

namespace ContestApp\ViewPage;

class AccountInfoPage {

	// 계정 정보 페이지
  private const VIEW_INFO_PATH = \APP_VIEW_PATH . "/show_account.html";
  public static function page(Array $accountInfo): String {
    return ViewPage::assemble(
      sprintf(ViewPage::read(self::VIEW_INFO_PATH),
        $accountInfo["phone"],
        $accountInfo["name"],
        $accountInfo["college"],
        ($accountInfo["sex"] ? "여" : "남"),
        $accountInfo["email"],
        $accountInfo["major"],
        $accountInfo["birthday"],
        $accountInfo["rating"]
      )
    );
  }

};
