<?php

/** 
 * 앱 설정 파일
 */

const SERVICE_NAME             = "Teammate";
const SERVICE_DESCRIPTION      = "공모전, 대회, 해커톤 팀원 모집";

const APP_ASSETS_PATH          = __DIR__ . "/assets";           // 앱이 참고할 정적 리소스 경로
const APP_ASSETS_VIEW_PATH     = APP_ASSETS_PATH . "/views";    // 앱이 참고할 html 파일 경로
const APP_ASSETS_IMAGE_PATH    = APP_ASSETS_PATH . "/images";   // 앱이 참고할 이미지 파일 경로
const APP_ASSETS_JS_PATH       = APP_ASSETS_PATH . "/js";       // 앱이 참고할 스크립트 경로

const WEB_ASSETS_PATH          = "/assets";                     // 클라이언트가 요청할 에셋 경로
const WEB_ASSETS_IMAGE_PATH    = WEB_ASSETS_PATH . "/images";   // 클라이언트가 요청할 이미지 에셋 경로
const WEB_ASSETS_JS_PATH       = WEB_ASSETS_PATH . "/js";       // 클라이언트가 요청할 스크립트 경로

const MYSQL_HOSTNAME           = "localhost";                   // MySQL 서버 주소
const MYSQL_DBNAME             = "contest";                     // MySQL DB 이름
const MYSQL_USERNAME           = "contest";                     // MySQL DB 사용자명
const MYSQL_PASSWORD           = "asdf1234";                    // MySQL DB 패스워드
