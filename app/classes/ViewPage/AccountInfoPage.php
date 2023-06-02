<?php

namespace ContestApp\ViewPage;
use \ContestApp\Model\AccountAttribute;

class AccountInfoPage {

  private static function loadRatingDetails(int $ratingPoint): Array {

    $ratingMessage = "평가 없음";
    $ratingColor = "grey";

    if ($ratingPoint <= -2) {
      $ratingMessage = "평가 나쁨";
      $ratingColor = "orange";
    } else if ($ratingPoint >= 0 && $ratingPoint <= 5) {
      $ratingMessage = "평가 보통";
      $ratingColor = "green";
    } else if ($ratingPoint > 5) {
      $ratingMessage = "평가 좋음";
      $ratingColor = "blue";
    }
    $ratingMessage = sprintf("%s (%d점)", $ratingMessage, $ratingPoint);

    return [ "message" => $ratingMessage, "color" => $ratingColor ];

  }

	// 계정 정보 페이지
  private const VIEW_INFO_PATH = \APP_VIEW_PATH . "/show_account.html";
  public static function page(Array $accountInfo, Array $collegesList, Array $majorsList): String {

    $ratingInfo = self::loadRatingDetails($accountInfo[AccountAttribute::Rating->value]);

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
        $ratingInfo["color"], $ratingInfo["message"]
      )
    );
  }

};
