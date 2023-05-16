<?php

namespace ContestApp\ViewPage;

class HomePage {

	// 홈 페이지
	private const VIEW_HOME_PATH = \APP_VIEW_PATH . "/tmp_home.html";
	public static function page(): String {
		return ViewPage::assemble(ViewPage::read(self::VIEW_HOME_PATH));
	}

};
