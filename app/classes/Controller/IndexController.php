<?php

namespace ContestApp\Controller;
use ContestApp\Http\{Header, StatusCode, Router};
use ContestApp\Resource\MimeType;
use ContestApp\ViewPage\{HomePage, ErrorPage};

/**
 * 루트 경로 요청 컨트롤러
 */
class IndexController {

	// index.* 요청시 홈 페이지로 리디렉션
	public static function redirect(): void {
		StatusCode::setStatusCode(StatusCode::MOVED_PERMANENTLY);
		Header::setHeader(Header::LOCATION, Router::PATH_HOME_PAGE);
	}

	// favicon.ico 요청시 에셋 디렉터리로 리디렉션
	public static function favicon(): void {
		StatusCode::setStatusCode(StatusCode::MOVED_PERMANENTLY);
		Header::setHeader(Header::LOCATION,
			sprintf("%s%s", WEB_ASSETS_IMAGE_PATH, Router::PATH_FAVICON_IMAGE)
		);
	}

	// robots.txt 응답
	public static function robots(): void {
		StatusCode::setStatusCode(StatusCode::OK);
		Header::setHeader(Header::CONTENT_TYPE, MimeType::_TXT->value);
		printf("User-Agent: *\r\nDisallow: /\r\n");
	}

	// 홈 페이지 응답
	public static function home(): void {
		StatusCode::setStatusCode(StatusCode::OK);
		Header::setHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);
		printf("%s", HomePage::page());
	}

	// 라우터에 정의되지 않은 경로로 요청 발생시 Bad Request 응답
	public static function index(): void {
		StatusCode::setStatusCode(StatusCode::BAD_REQUEST);
		Header::setHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);
		printf("%s", ErrorPage::BadRequest());
	}

};
