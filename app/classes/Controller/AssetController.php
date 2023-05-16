<?php

namespace ContestApp\Controller;
use ContestApp\Http\{Header, StatusCode};
use ContestApp\Resource\{MimeType, StaticResource};
use ContestApp\ViewPage\ErrorPage;
use ContestApp\System\Time;

/**
 * 정적 리소스 요청 컨트롤러
 */
class AssetController {

	public static function handleAssets(String $resourcePath = ""): void {

		$resourceContents = "";

		try {

			if (strlen($resourcePath) === 0) {           // 요청 리소스가 정의되지 않은 경우
				throw new \InvalidArgumentException();
			}

			$resource = new StaticResource(sprintf("%s/%s", \APP_ASSETS_PATH, $resourcePath));
			$lastModified = Time::DateRFC2822($resource->getLastModified());

			StatusCode::setServerStatusCode(StatusCode::OK);
			Header::setServerHeader(Header::CONTENT_TYPE, $resource->getMimeType()->value);
			Header::setServerHeader(Header::LAST_MODIFIED, $lastModified);

			$resourceContents = $resource->getContents();

		} catch (\InvalidArgumentException) {          // 요청 리소스가 없는 경우

			StatusCode::setServerStatusCode(StatusCode::NOT_FOUND);
			Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);
			$resourceContents = ErrorPage::NotFound();

		} catch (\Throwable) {                         // 또는 처리중 오류가 발생한 경우

			StatusCode::setServerStatusCode(StatusCode::INTERNAL_SERVER_ERROR);
			Header::setServerHeader(Header::CONTENT_TYPE, MimeType::_HTML->value);
			$resourceContents = ErrorPage::InternalServerError();

		}

		printf("%s", $resourceContents);

	}

};
