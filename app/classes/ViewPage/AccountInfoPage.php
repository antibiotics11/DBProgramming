<?php

namespace ContestApp\ViewPage;
use \ContestApp\Model\AccountAttribute;

class AccountInfoPage {

	// 계정 정보 페이지
  private const VIEW_INFO_PATH = \APP_VIEW_PATH . "/show_account.html";
  public static function page(Array $accountInfo, Array $collegesList, Array $majorsList): String {
    return ViewPage::assemble(
      sprintf(ViewPage::read(self::VIEW_INFO_PATH),
        $accountInfo[AccountAttribute::Phone->value],
        $accountInfo[AccountAttribute::Name->value],
        ViewPage::loadSelectedOption((int)$accountInfo[AccountAttribute::College->value], $collegesList),
        (
          $accountInfo[AccountAttribute::Sex->value] ? "여" : "남"
        ),
        $accountInfo[AccountAttribute::Email->value],
        ViewPage::loadSelectedOption((int)$accountInfo[AccountAttribute::Major->value], $majorsList),
        $accountInfo[AccountAttribute::Birthday->value],
        (
          ($accountInfo[AccountAttribute::Rating->value] == -1) ?
          "점수 없음" : sprintf("%s점", $accountInfo[AccountAttribute::Rating->value])
        )
      )
    );
  }

};
