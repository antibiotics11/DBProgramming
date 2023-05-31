<?php

namespace ContestApp\ViewPage;
use ContestApp\Model\{AccountAttribute, ContestAttribute};

class ContestInfoPage {

  private const VIEW_CONTEST_INFO_PATH = \APP_VIEW_PATH . "/contestinfo.html";
  private static function loadContestInfo(
    Array $contestInfo, Array $memberInfo,
    Array $majorsList, Array $regionsList, Array $collegesList,
    bool $isCreator
  ): String {

    $intramural = (bool)$contestInfo[ContestAttribute::Intramural->value];
    $regionsList[] = [ "code" => -1, "name" => "무관" ];

    $code      = (int)$contestInfo[ContestAttribute::Code->value];
    $phone     = $contestInfo[ContestAttribute::Phone->value];
    $done      = (int)$contestInfo[ContestAttribute::Done->value];
    $title     = $contestInfo[ContestAttribute::Title->value];
    $field     = ViewPage::loadSelectedOption((int)$contestInfo[ContestAttribute::Field->value], $majorsList);
    $headcount = $contestInfo[ContestAttribute::Headcount->value];
    $beginning = $contestInfo[ContestAttribute::Beginningdate->value];
    $deadline  = $contestInfo[ContestAttribute::Deadline->value];
    $college   = $intramural ? ViewPage::loadSelectedOption((int)$memberInfo["college"], $collegesList) : "무관";
    $region    = ViewPage::loadSelectedOption((int)$contestInfo[ContestAttribute::Region->value], $regionsList);
    $rating    = match ((int)$contestInfo[ContestAttribute::Rating->value]) {
      0 => "무관", 1 => "좋음", 2 => "보통 이상", 3 => "나쁨 이상"
    };

    $buttonValue = "참여신청";
    if ((int)$contestInfo[ContestAttribute::Done->value]) {
      $buttonValue = "모집완료";
    }

    $contestInfo = sprintf(ViewPage::read(self::VIEW_CONTEST_INFO_PATH),
      $code, $phone, $done, $title, $field,
      $headcount, $beginning, $deadline,
      $college, $region, $rating,
      $buttonValue
    );
    return $contestInfo;

  }

  public static function page(
    Array $contestInfo, Array $memberInfo,
    Array $majorsList, Array $regionsList, Array $collegesList,
    bool $isCreator = false
  ): String {
    return ViewPage::assemble(
      self::loadContestInfo($contestInfo, $memberInfo, $majorsList, $regionsList, $collegesList, $isCreator)
    );
  }

};
