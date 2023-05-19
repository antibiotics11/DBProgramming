<?php

namespace ContestApp\Model;

enum ContestAttribute: String {

  case Code          = "code";              // 코드
  case Phone         = "phone";             // 등록자 휴대폰 번호
  case Title         = "title";             // 제목
  case Field         = "field";             // 모집 분야
  case Headcount     = "headcount";         // 모집 인원수
  case Deadline      = "deadline";          // 모집 종료 기한
  case Beginningdate = "beginningdate";     // 모집 시작일
  case Done          = "done";              // 모집 종료 여부
  case Intramural    = "intramural";        // 참여 (등록자 기준)교내외 조건
  case Rating        = "rating";            // 참여 조건 점수
  case Region        = "region";            // 참여 조건 지역

  public static function trimValue(String $attribute, String $value): Mixed {

    switch ($attribute) {

      case self::Code->value :
      case self::Field->value :
      case self::Headcount->value :
      case self::Rating->value :
      case self::Region->value :
        return (int)trim($value);

      case self::Phone->value :
        $value = preg_replace("/\D/", "", $value);
      case self::Title->value :
      case self::Deadline->value :
      case self::Beginningdate->value :
        return (String)trim($value);

      case self::Done->value :
      case self::Intramural->value :
        return (bool)trim($value);

      default :
        return (String)trim($value);

    };

  }

};
