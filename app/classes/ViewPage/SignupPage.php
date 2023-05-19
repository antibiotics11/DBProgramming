<?php

namespace ContestApp\ViewPage;
use \ParseCSV\CSV;

class SignupPage {

	private static function loadSelectOptions(Array $optionsList): String {

		$options = "";
		for ($o = 0; $o < count($optionsList); $o++) {

			$optionCode = (int)$optionsList[$o]["code"];
			$optionName = (String)$optionsList[$o]["name"];

			$option = sprintf("<option value=\"%d\">%s</option>", $optionCode, $optionName);
			$options = sprintf("%s\r\n%s", $options, $option);
			
		}

		return $options;

	}

	// 회원가입 페이지
	private const VIEW_SIGNUP_PATH = \APP_VIEW_PATH . "/signup.html";
	public static function page(Array $collegesList, Array $majorsList): String {
		return ViewPage::assemble(
			sprintf(ViewPage::read(self::VIEW_SIGNUP_PATH),
				self::loadSelectOptions($collegesList),
				self::loadSelectOptions($majorsList)
			)
		);
	}

};
