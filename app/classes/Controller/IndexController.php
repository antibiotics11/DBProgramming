<?php

namespace ContestApp\Controller;
use ContestApp\Http\{Header, StatusCode};
use ContestApp\Resource\MimeType;
use ContestApp\ViewPage\{HomePage, ErrorPage};

/**
 * 루트 경로 요청 컨트롤러
 */
class IndexController {

	// index.* 요청시 홈 페이지로 리디렉션
	public static function redirect(): void {
		StatusCode::setServerStatusCode(StatusCode::MOVED_PERMANENTLY);
		Header::setServerHeader(Header::LOCATION, "/home");
	}

	// favicon.ico 요청시 에셋 디렉터리로 리디렉션
	public static function favicon(): void {
		StatusCode::setServerStatusCode(StatusCode::MOVED_PERMANENTLY);
		Header::setServerHeader(Header::LOCATION, sprintf("%s/image/favicon.ico", \WEB_ASSETS_PATH));
	}

	// robots.txt 응답
	public static function robots(): void {
		StatusCode::setServerStatusCode(StatusCode::OK);
		Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_TXT->value);
		printf("User-Agent: *\r\nDisallow: /\r\n");
	}

	// 홈 페이지 응답*/
	public static function viewHome(): void {
		StatusCode::setServerStatusCode(StatusCode::OK);
		Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);
		printf("%s", HomePage::page());
	}

	// 라우터에 정의되지 않은 경로로 요청 발생시 Bad Request 응답
	public static function index(): void {
		StatusCode::setServerStatusCode(StatusCode::BAD_REQUEST);
		Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);
		printf("%s", ErrorPage::BadRequest());
	}

};
