<?php

namespace ContestApp\ViewPage;

class SigninPage {

	// 로그인 페이지
	private const VIEW_SIGNIN_PATH = \APP_VIEW_PATH . "/signin.html";
	public static function page(): String {
		return ViewPage::assemble(ViewPage::read(self::VIEW_SIGNIN_PATH));
	}

};
