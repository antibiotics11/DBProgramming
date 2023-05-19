<?php

namespace ContestApp\Model;

enum AccountAttribute: String {

  case Phone    = "phone";        // 휴대폰 번호
  case Password = "password";     // 패스워드
  case Name     = "name";         // 이름
  case College  = "college";      // 대학
  case Sex      = "sex";          // 성별
  case Email    = "email";        // 이메일 주소
  case Major    = "major";        // 전공분야
  case Birthday = "birthday";     // 생년월일
  case Rating   = "rating";       // 사용자 점수

  public static function trimValue(String $attribute, String $value): Mixed {

    switch ($attribute) {

      case self::Phone->value :
        $value = preg_replace("/\D/", "", $value);
      case self::Password->value :
      case self::Name->value :
      case self::Email->value :
      case self::Birthday->value :
        return (String)trim($value);

      case self::College->value :
      case self::Major->value :
        return (int)trim($value);

      case self::Sex->value :
        return (bool)trim($value);

      default :
        return (String)trim($value);

    };

  }

};
