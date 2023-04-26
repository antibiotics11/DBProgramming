<?php

namespace ContestApp\ViewPage;
use ContestApp\Http\StatusCode;
use ContestApp\Resource\HttpErrorPage;

class ErrorPage extends ViewPage {

	private const DETAILS_FORBIDDEN              = "요청이 거부되었습니다.";
	private const DETAILS_NOT_FOUND              = "요청하신 리소스를 찾을 수 없습니다.";
	private const DETAILS_BAD_REQUEST            = "요청이 잘못된 형식입니다.";
	private const DETAILS_INTERNAL_SERVER_ERROR  = "서버 내부 오류가 발생했습니다.";

	// Forbidden 페이지
	public static function Forbidden(): String {
		return parent::assemble(HttpErrorPage::Forbidden(self::DETAILS_FORBIDDEN));
	}

	// Not Found 페이지
	public static function NotFound(): String {
		return parent::assemble(HttpErrorPage::NotFound(self::DETAILS_NOT_FOUND));
	}

	// Bad Request 페이지
	public static function BadRequest(): String {
		return parent::assemble(HttpErrorPage::BadRequest(self::DETAILS_BAD_REQUEST));
	}

	// Internal Server Error 페이지
	public static function InternalServerError(): String {
		return parent::assemble(HttpErrorPage::InternalServerError(self::DETAILS_INTERNAL_SERVER_ERROR));
	}

};
