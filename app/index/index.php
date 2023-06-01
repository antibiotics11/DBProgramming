<?php

declare(ticks = 1, strict_types = 1);

require_once "../autoloader.php";
require_once "../vendor/autoload.php";
require_once "../config.php";

try {                               // 라우터를 생성하고 요청을 할당한다.

	\ContestApp\System\Time::setTimeZone(\SERVER_DEFAULT_TIMEZONE);
	(new \ContestApp\Http\Router)->routeRequest();

} catch (\Throwable $e) {           // 요청 처리중 예외 또는 오류 발생했다면

	$status = \ContestApp\Http\StatusCode::INTERNAL_SERVER_ERROR;
	$message = \ContestApp\Http\StatusCode::toMessage($status);

	\ContestApp\Http\StatusCode::setServerStatusCode($status);
	printf("%s", \SERVER_PRINT_EXCEPTION ? $e : $message);

}
