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

		$this->router->get("/robots.txt",           [IndexController::class, "robots"]);                // robots.txt 조회
		$this->router->get("/favicon.ico",          [IndexController::class, "favicon"]);               // 파비콘 조회

		$this->router->get("/",                     [IndexController::class, "redirect"]);              // 리디렉션
		$this->router->get("/index([^/]+)",         [IndexController::class, "redirect"]);              // 리디렉션
		$this->router->get("/home",                 [IndexController::class, "viewHome"]);              // 홈 페이지 조회
		$this->router->get("/jonggang",             [IndexController::class, "easteregg"]);

		$this->router->get("/assets/(.*)",          [AssetController::class, "handleAssets"]);          // 정적 리소스 조회

		$this->router->post("/account/signin",      [AccountController::class, "handleSignin"]);        // 로그인 요청 처리
		$this->router->post("/account/signup",      [AccountController::class, "handleSignup"]);        // 회원가입 요청 처리
		$this->router->post("/account/eval",        [AccountController::class, "handleEval"]);          // 회원 평가
		$this->router->get("/account/signout",      [AccountController::class, "handleSignout"]);       // 로그아웃 요청 처리
		$this->router->get("/account/signin",       [AccountController::class, "viewSignin"]);          // 로그인 페이지 조회
		$this->router->get("/account/signup",       [AccountController::class, "viewSignup"]);          // 회원가입 페이지 조회
		$this->router->get("/account/info/{phone}", [AccountController::class, "viewMemberInfo"]);      // 게정정보 조회 (다른 회원)
		$this->router->get("/account/info",         [AccountController::class, "viewAccountInfo"]);     // 계정정보 조회 (회원 본인)
		$this->router->get("/account",              [AccountController::class, "redirect"]);            // 리디렉션

		$this->router->post("/contest/apply",       [ContestController::class, "handleApply"]);         // 공모전 참가 요청 처리
		$this->router->post("/contest/create",      [ContestController::class, "handleCreate"]);        // 공모전 등록 요청 처리
		$this->router->post("/contest/update",      [ContestController::class, "handleUpdate"]);        // 공모전 수정 요청 처리
		$this->router->post("/contest/delete",      [ContestController::class, "handleDelete"]);        // 공모전 삭제 요청 처리
		$this->router->get("/contest/create",       [ContestController::class, "viewCreate"]);          // 공모전 등록 페이지 조회
		$this->router->get("/contest/v/{code}",     [ContestController::class, "viewContestDetail"]);   // 공모전 정보 조회
		$this->router->get("/contest/v",            [ContestController::class, "redirectToList"]);      // 리디렉션
		$this->router->get("/contest/list",         [ContestController::class, "viewContestList"]);     // 공모전 목록 조회
		$this->router->get("/contest",              [ContestController::class, "redirectToList"]);      // 리디렉션

		$this->router->all(".*",                    [IndexController::class, "index"]);

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
