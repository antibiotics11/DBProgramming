<?php

namespace ContestApp\Controller;
use ContestApp\Http\{Header, StatusCode};
use ContestApp\Resource\{MimeType, StaticResource};
use ContestApp\ViewPage\ErrorPage;
use ContestApp\System\Time;

/**
 * 정적 리소스 요청 컨트롤러
 */
class Assets {

	public static function assets(String $name = ""): void {

		try {

			if (strlen($name) === 0) {                               // 요청 리소스가 정의되지 않은 경우
				throw new \InvalidArgumentException();
			}

			$resource = new StaticResource(sprintf("%s/%s", \APP_ASSETS_PATH, $name));
			$lastModified = Time::DateRFC2822($resource->getLastModified());

			StatusCode::setStatusCode(StatusCode::OK);
			Header::setHeader(Header::CONTENT_TYPE, $resource->getMimeType()->value);
			Header::setHeader(Header::LAST_MODIFIED, $lastModified);
			printf("%s", $resource->getContents());

		} catch (\InvalidArgumentException $e) {                    // 요청 리소스가 없는 경우

			StatusCode::setStatusCode(StatusCode::NOT_FOUND);
			Header::setHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);
			printf("%s", ErrorPage::NotFound());

		} catch (\Throwable $e) {                                   // 또는 처리중 어떤 오류가 발생한 경우

			StatusCode::setStatusCode(StatusCode::INTERNAL_SERVER_ERROR);
			Header::setHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);
			printf("%s", ErrorPage::InternalServerError());

		}

	}

};
