<?php

namespace ContestApp\ViewPage;
use ContestApp\Http\StatusCode;
use ContestApp\Resource\{HtmlResource};

class Viewpage {

	/**
	 * html 형식의 view 파일을 가져온다. (file_get_contents 대신 사용)
	 *
	 * @param String $viewPath 파일 경로
	 * @param String 파일 내용
	 */
	public static function read(String $viewPath = ""): String {

		$viewContents = "";
		if (!is_file($viewPath) || !is_readable($viewPath)) {
			$code = StatusCode::NOT_FOUND;
			$viewContents = ErrorPage::loadContainer($code->value, StatusCode::toMessage($code));
		} else {
			$viewContents = file_get_contents($viewPath);
			if ($viewContents === false) {
				$code = StatusCode::INTERNAL_SERVER_ERROR;
				$viewContents = ErrorPage::loadContainer($code->value, StatusCode::toMessage($code));
			}
		}

		return $viewContents;

	}

	/**
	 * html 페이지를 조립하여 반환한다.
	 *
	 * @param String $container header와 footer사이에 삽입할 container
	 * @return String 완성된 html 페이지
	 */
	private const VIEW_MAIN_PATH = \APP_VIEW_PATH . "/main.html";
	public static function assemble(String $container = ""): String {

		$page = new HtmlResource;

		$page->setLanguage("ko-KR");
		$page->pushHead(sprintf("<title>%s</title>", \SERVICE_NAME));
		$page->pushHead(sprintf("<meta name=\"title\" content=\"%s\">", \SERVICE_NAME));
		$page->pushHead(sprintf("<meta name=\"description\" content=\"%s\">", \SERVICE_DESCRIPTION));
		$page->pushHead(sprintf("<meta property=\"og:title\" content=\"%s\">", \SERVICE_NAME));
		$page->pushHead(sprintf("<meta property=\"og:description\" content=\"%s\">", \SERVICE_DESCRIPTION));
		$page->pushHead(sprintf("<meta property=\"og:url\" content=\"https://%s\">", \SERVICE_URL));
		$page->pushHead("<meta property=\"og:type\" content=\"website\">");
		$page->pushHead("<link rel=\"stylesheet\" href=\"/assets/style.css\">");
		$page->pushHead("<link rel=\"shortcut icon\" href=\"/assets/image/favicon.ico\">");

		$page->setBody(sprintf(self::read(self::VIEW_MAIN_PATH), $container));

		return $page->pack();

	}

};
