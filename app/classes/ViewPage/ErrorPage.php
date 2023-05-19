<?php

namespace ContestApp\ViewPage;
use ContestApp\Http\StatusCode;

class ErrorPage {

	/**
   * 응답 페이지의 Container 영역을 생성
   *
   * @param int $code 상태 코드
   * @param String $message 상태 메시지
   * @param String $details 더 자세한 메시지
   * @return String 생성된 Container
   */
  public static function loadContainer(int $code, String $message, String $details = ""): String {
    return sprintf("<div id = \"container\">%s\r\n%s</div>",
      "<link rel=\"stylesheet\" href=\"/assets/error_style.css\">",
      sprintf("<div id = \"status\">%s<br /><br />%s</div>",
        sprintf("<div id = \"status-message\">%d %s</div>", $code, $message),
        sprintf("<div id = \"status-details\">%s</div>", $details)
      )
    );
  }


	// Forbidden 페이지
	private const ERROR_DETAILS_FORBIDDEN = "요청하신 리소스에 접근할 수 없습니다.";
	public static function Forbidden(): String {
		$code = StatusCode::FORBIDDEN;
		return ViewPage::assemble(
			self::loadContainer($code->value, StatusCode::toMessage($code), self::ERROR_DETAILS_FORBIDDEN)
		);
	}

	// Not Found 페이지
	private const ERROR_DETAILS_NOT_FOUND = "요청하신 리소스를 찾을 수 없습니다.";
	public static function NotFound(): String {
		$code = StatusCode::NOT_FOUND;
		return ViewPage::assemble(
			self::loadContainer($code->value, StatusCode::toMessage($code), self::ERROR_DETAILS_NOT_FOUND)
		);
	}

	// Bad Request 페이지
	private const ERROR_DETAILS_BAD_REQUEST = "잘못된 요청 형식입니다.";
	public static function BadRequest(): String {
		$code = StatusCode::BAD_REQUEST;
		return ViewPage::assemble(
			self::loadContainer($code->value, StatusCode::toMessage($code), self::ERROR_DETAILS_BAD_REQUEST)
		);
	}

	// Internal Server Error 페이지
	private const ERROR_DETAILS_INTERNAL_SERVER_ERROR = "서버 오류가 발생했습니다.";
	public static function InternalServerError(): String {
		$code = StatusCode::INTERNAL_SERVER_ERROR;
		return ViewPage::assemble(
			self::loadContainer($code->value, StatusCode::toMessage($code), self::INTERNAL_SERVER_ERROR)
		);
	}

};
