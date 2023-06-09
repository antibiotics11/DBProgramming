<?php

namespace ContestApp\ViewPage;
use ContestApp\Model\{AccountAttribute, ContestAttribute};

class ContestInfoPage {

  private const VIEW_CONTEST_INFO_PATH = \APP_VIEW_PATH . "/contestinfo.html";
  private static function loadContestInfo(
    Array $contestInfo, Array $memberInfo, Array $creatorInfo,
    Array $majorsList, Array $regionsList, Array $collegesList,
    bool $isCreator, bool $applied
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
    $college   = $intramural ? ViewPage::loadSelectedOption((int)$creatorInfo["college"], $collegesList) : "무관";
    $regions   = ViewPage::loadSelectOptions($regionsList);
    $region    = (int)$contestInfo[ContestAttribute::Region->value];
    $rating    = (int)$contestInfo[ContestAttribute::Rating->value];

    $buttonValue = $applied ? "참여취소" : "참여신청";
    $contestInfo = sprintf(ViewPage::read(self::VIEW_CONTEST_INFO_PATH),
      $code, $phone, $done, $title, $field,
      $headcount, $beginning, $deadline,
      $college, $regions,
      $buttonValue,
      $region, $rating
    );
    return $contestInfo;

  }

  public static function page(
    Array $contestInfo, Array $memberInfo, Array $creatorInfo,
    Array $majorsList, Array $regionsList, Array $collegesList,
    bool $isCreator = false, bool $applied = false
  ): String {
    return ViewPage::assemble(
      self::loadContestInfo(
        $contestInfo, $memberInfo, $creatorInfo,
        $majorsList, $regionsList, $collegesList,
        $isCreator, $applied
      )
    );
  }

};
