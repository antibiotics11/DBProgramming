<?php

namespace ContestApp\ViewPage;

class HomePage extends ViewPage {

	// 홈 페이지
	public static function page(): String {
		return parent::assemble(
			file_get_contents(sprintf("%s/home", \APP_ASSETS_VIEW_PATH))
		);
	}

};
