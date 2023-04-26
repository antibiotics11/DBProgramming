<?php

namespace ContestApp\Resource;
use ContestApp\Http\StatusCode;

class HttpErrorPage {

	private HtmlResource $page;
	private String       $stylesheet;

	public function __construct(int $code, String $message, String $details = "") {
		$this->page = new HtmlResource();
		$this->setPage($code, $message, $details);
	}

	public function setStylesheet(String $css): void {
		$this->page->pushHead(sprintf("<style type=\"text/css\">\r\n%s\r\n</style>", $css));
	}

	public function getStylesheet(): String {
		return $this->stylesheet;
	}

	public function setPage(int $code, String $message, String $details = ""): void {

		$this->page->pushHead(sprintf("<title>%d %s</title>", $code, $message));
		$this->page->setBody(
			sprintf("<div id = \"container\">%s</div>",
				sprintf("<div id = \"status\">%s<br /><br />%s</div>",
					sprintf("<div id = \"status-message\">%d %s</div>", $code, $message),
					sprintf("<div id = \"status-details\">%s</div>", $details)
				)
			)
		);

	}

	public function getPage(): String {
		return $this->page->pack();
	}

	public static function Forbidden(String $details = ""): String {
		return (
			new HttpErrorPage(StatusCode::FORBIDDEN->value, "Forbidden", $details)
		)->getPage();
	}

	public static function NotFound(String $details = ""): String {
		return (
			new HttpErrorPage(StatusCode::NOT_FOUND->value, "Not Found", $details)
		)->getPage();
	}

	public static function MethodNotAllowed(String $details = ""): String {
		return (
			new HttpErrorPage(StatusCode::METHOD_NOT_ALLOWED->value, "Method Not Allowed", $details)
		)->getPage();
	}

	public static function BadRequest(String $details = ""): String {
		return (
			new HttpErrorPage(StatusCode::BAD_REQUEST->value, "Bad Request", $details)
		)->getPage();
	}

	public static function InternalServerError(String $details = ""): String {
		return (
			new HttpErrorPage(StatusCode::INTERNAL_SERVER_ERROR->value, "Internal Server Error", $details)
		)->getPage();
	}

};

