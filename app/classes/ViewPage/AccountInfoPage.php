<?php

namespace ContestApp\ViewPage;

class AccountInfoPage {

	// 계정 정보 페이지
	private const VIEW_INFO_PATH = \APP_VIEW_PATH . "show_account.html";
	public static function page(Array $accountInfo = []): String {
		return ViewPage::assemble(ViewPage::read(self::VIEW_SIGNIN_PATH));
	}

};
