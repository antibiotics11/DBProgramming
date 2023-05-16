<?php

namespace ContestApp\Http;
use ContestApp\Controller\{IndexController, AccountController, ContestController, AssetController};

/**
 * 앱으로 들어오는 Http 요청을 라우팅
 */
class Router {

	private \Bramus\Router\Router $router;

	// 요청을 제어할 모든 경로를 설정
	private function routing(): void {

		$this->router->get("/robots.txt",         [IndexController::class, "robots"]);
		$this->router->get("/favicon.ico",        [IndexController::class, "favicon"]);

		$this->router->get("/",                   [IndexController::class, "redirect"]);
		$this->router->get("/index([^/]+)",       [IndexController::class, "redirect"]);
		$this->router->get("/home",               [IndexController::class, "viewHome"]);

		$this->router->get("/assets/(.*)",        [AssetController::class, "handleAssets"]);

		$this->router->post("/account/signin",    [AccountController::class, "handleSignin"]);
		$this->router->post("/account/signup",    [AccountController::class, "handleSignup"]);
		$this->router->get("/account/signout",    [AccountController::class, "handleSignout"]);
		$this->router->get("/account/signin",     [AccountController::class, "viewSignin"]);
		$this->router->get("/account/signup",     [AccountController::class, "viewSignup"]);
		$this->router->get("/account/info",       [AccountController::class, "viewAccountInfo"]);
		$this->router->get("/account",            [AccountController::class, "redirect"]);

		$this->router->all(".*",                  [IndexController::class, "index"]);

	}

	public function __construct() {
		$this->router = new \Bramus\Router\Router();
	}

	// 요청을 라우터에 할당
	public function routeRequest(): void {
		$this->routing();
		$this->router->run();
	}

};
