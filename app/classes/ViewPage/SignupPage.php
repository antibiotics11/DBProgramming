<?php

namespace ContestApp\ViewPage;

class SignupPage {

	// 회원가입 페이지
	private const VIEW_SIGNUP_PATH = \APP_VIEW_PATH . "/signup.html";
	public static function page(Array $collegesList, Array $majorsList): String {
		return ViewPage::assemble(
			sprintf(ViewPage::read(self::VIEW_SIGNUP_PATH),
				ViewPage::loadSelectOptions($collegesList),
				ViewPage::loadSelectOptions($majorsList)
			)
		);
	}

};
