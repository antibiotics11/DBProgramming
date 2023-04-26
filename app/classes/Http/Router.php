<?php

namespace ContestApp\Http;
use ContestApp\Controller\{Index, Account, Contest, Assets};

/**
 * 앱으로 들어오는 Http 요청을 라우팅
 */
class Router {

	private \Bramus\Router\Router $router;
	
	private Array $serverParams      = [];
	private Array $getParams         = [];
	private Array $postParams        = [];
	private Array $cookieParams      = [];
	private Array $uploadFileParams  = [];

	// 요청을 제어할 모든 경로를 설정
	private function routing(): void {
	
		$router = &$this->router;
	
		$router->get(self::PATH_ROBOTS_TXT,      [Index::class, "robots"]);
		$router->get(self::PATH_FAVICON_IMAGE,   [Index::class, "favicon"]);
		
		$router->get(self::PATH_ROOT,            [Index::class, "redirect"]);
		$router->get(self::PATH_INDEX_PAGE,      [Index::class, "redirect"]);
		$router->get(self::PATH_HOME_PAGE,       [Index::class, "home"]);
		
		$router->get(self::PATH_ASSETS_CONTENTS, [Assets::class, "assets"]);

		$router->all(".*",                       [Index::class, "index"]);
	
	}

	public function __construct() {
		$this->router = new \Bramus\Router\Router();
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

	public const PATH_ROBOTS_TXT      = "/robots.txt";
	public const PATH_FAVICON_IMAGE   = "/favicon.ico";
	public const PATH_ROOT            = "/";
	public const PATH_INDEX_PAGE      = "/index([^/]+)";
	public const PATH_HOME_PAGE       = "/home";
	public const PATH_ASSETS_CONTENTS = "/assets/(.*)";

};
