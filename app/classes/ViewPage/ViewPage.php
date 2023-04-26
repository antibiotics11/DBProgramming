<?php

namespace ContestApp\ViewPage;
use ContestApp\Resource\{HtmlResource, HttpErrorPage};

class Viewpage {

	private const HEADER_PATH     = \APP_ASSETS_VIEW_PATH . "/header";
	private const FOOTER_PATH     = \APP_ASSETS_VIEW_PATH . "/footer";
	private const STYLESHEET_PATH = \WEB_ASSETS_PATH . "/stylesheet.css";
	private const FAVICON_PATH    = \WEB_ASSETS_IMAGE_PATH . "/favicon.ico";

	/**
	 * html 페이지를 조립하여 반환한다.
	 *
	 * @param String $container header와 footer사이에 삽입할 container
	 * @return String 완성된 html 페이지
	 */
	protected static function assemble(String $container = ""): String {

		$page = new HtmlResource;
		$page->setLanguage("ko-KR");
		$page->pushHead(sprintf("<title>%s</title>", \SERVICE_NAME));
		$page->pushHead(sprintf("<meta name=\"title\" content=\"%s\">", \SERVICE_NAME));
		$page->pushHead(sprintf("<meta name=\"description\" content=\"%s\">", \SERVICE_DESCRIPTION));
		$page->pushHead(sprintf("<link rel=\"stylesheet\" href=\"%s\">", self::STYLESHEET_PATH));
		$page->pushHead(sprintf("<link rel=\"shortcut icon\" href=\"%s\">", self::FAVICON_PATH));

		$page->setBody(sprintf("%s\r\n%s\r\n%s", 
			file_get_contents(self::HEADER_PATH),
			$container,
			file_get_contents(self::FOOTER_PATH)
		));

		return $page->pack();

	}

};
