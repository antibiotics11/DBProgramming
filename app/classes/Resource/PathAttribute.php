<?php

namespace ContestApp\Resource;

/**
 * pathinfo에 반환되는 요소 및 기타 추가 속성을 정의
 */
enum PathAttribute: String {

  case BASENAME  = "basename";    // 파일 이름
  case EXTENSION = "extension";   // 파일 확장자
  case DIRNAME   = "dirname";     // 디렉터리
  case FILENAME  = "filename";    // 확장자를 제외한 파일 이름

  case RELATIVE  = "relative";    // (실행되는 위치에서) 상대 경로
  case ABSOLUTE  = "absolute";    // 시스템상의 절대 경로

  // pathinfo에 사용할 플래그를 반환
  public static function pathinfoFlags(?PathAttribute $attribute = null): int {

    return match ($attribute) {
      self::BASENAME  => \PATHINFO_BASENAME,
      self::EXTENSION => \PATHINFO_EXTENSION,
      self::DIRNAME   => \PATHINFO_DIRNAME,
      self::FILENAME  => \PATHINFO_FILENAME,
      default         => \PATHINFO_ALL
    };

  }

};

