<?php

namespace ContestApp\Http;
use ContestApp\Controller\{Index, Account, Contest, Assets};

/**
 * 앱으로 들어오는 Http 요청을 라우팅
 */
class Router {

	private \Bramus\Router\Router $router;

	private Array $serverParams;
	private Array $getParams;
	private Array $postParams;
	private Array $cookieParams;
	private Array $uploadFileParams;

	// 요청을 제어할 모든 경로를 설정
	private function routing(): void {

		$this->router->get("/robots.txt",         [Index::class, "robots"]);
		$this->router->get("/favicon.ico",        [Index::class, "favicon"]);

		$this->router->get("/",                   [Index::class, "redirect"]);
		$this->router->get("/index([^/]+)",       [Index::class, "redirect"]);
		$this->router->get("/home",               [Index::class, "home"]);

		$this->router->get("/assets/(.*)",        [Assets::class, "assets"]);

		$this->router->all(".*",                  [Index::class, "index"]);

	}

	public function __construct() {
		$this->router = new \Bramus\Router\Router();
		$this->setParams([], [], [], [], []);
	}

	// 라우터에 사전정의 변수를 설정
	public function setParams(
		Array $serverParams     = [],
		Array $getParams        = [],
		Array $postParams       = [],
		Array $cookieParams     = [],
		Array $uploadFileParams = [],
	): void {
		$this->serverParams     = $serverParams;
		$this->getParams        = $getParams;
		$this->postParams       = $postParams;
		$this->cookieParams     = $cookieParams;
		$this->uploadFileParams = $uploadFileParams;
	}

	// 요청을 라우터에 할당
	public function routeRequest(): void {
		$this->routing();
		$this->router->run();
	}

};
