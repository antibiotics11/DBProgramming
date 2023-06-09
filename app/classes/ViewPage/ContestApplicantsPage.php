<?php

namespace ContestApp\ViewPage;

class ContestApplicantsPage {

  private const VIEW_CONTEST_APPLICANTS_INFO = \APP_VIEW_PATH . "/applicantinfo.html";
  private static function loadApplicantsList(Array $applicantsList): String {

    $applicants = "";
    $phoneFormatter = fn($phone) => preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/", "$1-$2-$3", $phone);

    $applicantInfoForm = ViewPage::read(self::VIEW_CONTEST_APPLICANTS_INFO);
    $count = count($applicantsList);

    for ($a = 0; $a < $count; $a++) {

      $applicantInfo = sprintf($applicantInfoForm,
        $applicantsList[$a]["phone"], $applicantsList[$a]["name"],
        $applicantsList[$a]["phone"], $phoneFormatter($applicantsList[$a]["phone"]),
        $applicantsList[$a]["phone"],
        $applicantsList[$a]["phone"], $applicantsList[$a]["phone"],
        $applicantsList[$a]["phone"],
        (int)$applicantsList[$a]["rated"],
        $applicantsList[$a]["phone"], $applicantsList[$a]["phone"],
        $applicantsList[$a]["phone"], $applicantsList[$a]["phone"]
      );
      $applicants = sprintf("%s\r\n%s", $applicants, $applicantInfo);

    }

    if ($count == 0) {
      $applicants = "<script type=\"text/javascript\">alert(\"지원자가 없습니다.\");history.back()</script>";
    }

    return $applicants;

  }

  // 공모전 등록 페이지
  private const VIEW_CONTEST_APPLICANTS_PATH = \APP_VIEW_PATH . "/applicantlist.html";
  public static function page(Array $applicantsList): String {
    return ViewPage::assemble(
      sprintf(ViewPage::read(self::VIEW_CONTEST_APPLICANTS_PATH),
        self::loadApplicantsList($applicantsList)
      )
    );
  }

};
