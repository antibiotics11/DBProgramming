<?php

require_once "../autoloader.php";
require_once "../vendor/autoload.php";
require_once "../config.php";

\ContestApp\System\Time::setTimeZone("GMT");

try {                                 // 라우터를 생성하고 요청을 할당한다.

	$appRouter = new \ContestApp\Http\Router();

	$appRouter->setParams($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
	$appRouter->routeRequest();

} catch (\Throwable $e) {             // 요청 처리중 예외 또는 PHP오류 발생했다면

	\ContestApp\Http\StatusCode::setStatusCode(
		\ContestApp\Http\StatusCode::INTERNAL_SERVER_ERROR
	);
	printf("오류가 발생했습니다.<br />문의: cgh5325@kunsan.ac.kr");

}
