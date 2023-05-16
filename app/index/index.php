<?php

declare(ticks = 1, strict_types = 1);

require_once "../autoloader.php";
require_once "../vendor/autoload.php";
require_once "../config.php";

use \ContestApp\Http\{StatusCode, Router};

try {                               // 라우터를 생성하고 요청을 할당한다.

	\ContestApp\System\Time::setTimeZone("GMT");
	(new Router)->routeRequest();

} catch (\Throwable $e) {           // 요청 처리중 예외 또는 오류 발생했다면

	$status = StatusCode::INTERNAL_SERVER_ERROR;
	$message = StatusCode::toMessage($status);

	StatusCode::setServerStatusCode($status);
	printf("%s", $message);

}
