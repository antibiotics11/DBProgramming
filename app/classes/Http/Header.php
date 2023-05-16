<?php

namespace ContestApp\Http;

/**
 * 앱에서 직접 제어할 Http 헤더 속성을 정의
 */
enum Header: String {

	case CONTENT_TYPE     = "Content-Type";         // 리소스 타입
	case LAST_MODIFIED    = "Last-Modified";        // 리소스의 최종 수정 시간
	case LOCATION         = "Location";             // 리디렉션 위치
	case CONTENT_LOCATION = "Content-Location";     // 요청 처리 후 리소스 위치

	/**
	 * 웹서버의 헤더를 설정
	 *
	 * @param Header $attribute 설정할 헤더 속성
	 * @param String $value 설정할 헤더 값
	 */
	public static function setServerHeader(Header $attribute, String $value): void {
		header(sprintf("%s: %s", $attribute->value, trim($value)));
	}

	/**
	 * 웹서버의 설정된 헤더 목록을 반환
	 *
	 * @param Header $attribute 특정 헤더를 찾는 경우 헤더 속성
	 * @return String | Array 특정 헤더를 찾는 경우 해당 헤더, 또는 전체 헤더 값들
	 */
	public static function getServerHeader(Header $attribute = null): String | Array {

		$headers = \headers_list();
		if ($attribute === null) {
			return $headers;
		}

		foreach ($headers as $header) {
			list($headerAttribute) = explode(":", $header);
			if (strcmp(trim($headerAttribute), $attribute) === 0) {
				return $header;
			}
		}

		return "";

	}

};
