<?php

namespace ContestApp\Http;

/**
 * 앱에서 직접 처리할 Http 상태 코드를 정의
 */
enum StatusCode: int {

	case OK                      = 200;      // 요청 처리 성공
	case ACCEPTED                = 201;      // 요청이 일단 수락됨
	case CREATED                 = 202;      // 요청 리소스를 성공적으로 생성함

	case MOVED_PERMANENTLY       = 301;      // 요청을 영구적으로 리디렉션
	case MOVED_TEMPORARILY       = 302;      // 요청을 일시적으로 리디렉션

	case BAD_REQUEST             = 400;      // 요청이 잘못된 형식
	case FORBIDDEN               = 403;      // 요청 처리가 거부됨
	case NOT_FOUND               = 404;      // 요청된 리소스를 찾을 수 없음
	case METHOD_NOT_ALLOWED      = 405;      // 요청 메소드가 허용되지 않음

	case INTERNAL_SERVER_ERROR   = 500;      // 요청을 처리하던중 오류 발생
	case NOT_IMPLEMENTED         = 501;      // 요청을 처리할 기능이 없음

	/**
	 * 상태 코드별 상태 메시지를 반환한다.
	 *
	 * @param StatusCode $statusCode 상태 코드
	 * @return String 상태 코드의 상태 메시지
	 */
	public static function toMessage(StatusCode $statusCode): String {
		return match ($statusCode) {
			self::OK                    => "OK",
			self::ACCEPTED              => "Accepted",
			self::CREATED               => "Created",
			self::MOVED_PERMANENTLY     => "Moved Permanently",
			self::MOVED_TEMPORARILY     => "Moved Temporarily",
			self::BAD_REQUEST           => "Bad Request",
			self::FORBIDDEN             => "Forbidden",
			self::NOT_FOUND             => "Not Found",
			self::METHOD_NOT_ALLOWED    => "Method Not Allowed",
			self::INTERNAL_SERVER_ERROR => "Internal Server Error",
			self::NOT_IMPLEMENTED       => "Not Implemented"
		};
	}

	/**
	 * 웹서버의 상태 코드를 설정한다.
	 *
	 * @param StatusCode $code 설정할 상태 코드
	 * @return int 상태 코드 설정에 성공했으면 코드를, 실패했으면 -1
	 */
	public static function setServerStatusCode(StatusCode $statusCode): void {
		\http_response_code($statusCode->value);
	}


	/**
	 * 웹서버의 현재 상태 코드를 StatusCode 객체로 가져온다.
	 *
	 * @return StatusCode 웹서버의 현재 상태 코드
	 */
	public static function getServerStatusCode(): StatusCode {

		$statusCode = \http_response_code();
		if ($statusCode === false) {
			return StatusCode::INTERNAL_SERVER_ERROR;
		}
		return StatusCode::try($statusCode);

	}

};
