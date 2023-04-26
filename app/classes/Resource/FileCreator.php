<?php

namespace ContestApp\Resource;

class FileCreator {

  private const MAX_FILE_SIZE = 1024 * 1024 * 1024;    // 파일 최대 크기
  private const DIRECTORY_PERMISSIONS = 0777;          // 디렉터리 생성시 퍼미션

  public const ERROR_CODE_NO_ERROR            = 0;     // 파일을 성공적으로 생성
  public const ERROR_CODE_CREATION_FAILED     = 1;     // 파일 생성 실패
  public const ERROR_CODE_ALREDAY_EXISTS      = 2;     // (overwrite=false인 경우) 같은 이름의 파일이 이미 존재함
  public const ERROR_CODE_DIR_CREATION_FAILED = 3;     // (createDirectory=true인 경우) 디렉터리 생성 실패
  public const ERROR_CODE_UNSUPPORTED_TYPE    = 4;     // (ignoreType=false인 경우) 파일 타입이 정의되지 않음
  public const ERROR_CODE_TOO_LARGE           = 5;     // 파일 용량이 MAX_FILE_SIZE값을 초과함

  private const ERROR_MESSAGE_UNDEFINED           = "Undefined";
  private const ERROR_MESSAGE_NO_ERROR            = "No Error";
  private const ERROR_MESSAGE_CREATION_FAILED     = "File Creation Failed";
  private const ERROR_MESSAGE_ALREADY_EXISTS      = "File Already Exists";
  private const ERROR_MESSAGE_DIR_CREATION_FAILED = "Directoy Creation Failed";
  private const ERROR_MESSAGE_UNSUPPORTED_TYPE    = "Unsupported Type";
  private const ERROR_MESSAGE_TOO_LARGE           = "File Too Large";


  private int $errorCode;    // 오류 코드

  /**
   * 파일을 생성한다
   *
   * @param  String  $path           파일 경로
   * @param  String  $contents       파일 내용
   * @param  String  $rootDirectory  최상위 디렉터리 (최종 파일 경로 = 최상위 디렉터리 + 파일 경로)
   * @param  bool  $createDirectory  경로상의 디렉터리가 없으면 새로 생성
   * @param  bool  $overwrite        파일이 존재하면 덮어쓰기
   * @param  bool  $ignoreType       파일 타입이 MimeType에 정의되어 있지 않아도 생성
   */
  public function __construct(
    String $path,
    String $contents = "",
    String $rootDirectory = "",
    bool $createDirectory = true,
    bool $overwrite = false,
    bool $ignoreType = false,
    bool $append = false
  ) {

    $this->errorCode = -1;

    if ($this->isTooLarge(strlen($contents))) {
      $this->errorCode = self::ERROR_CODE_TOO_LARGE;
      return;
    }

    $parsedPath = $this->parsePath($path, $rootDirectory);
    if (!$ignoreType &&
        !$this->isSupportedType($parsedPath[PathAttribute::EXTENSION->value])) {
      $this->errorCode = self::ERROR_CODE_UNSUPPORTED_TYPE;
      return;
    }

    $absolutePath = $parsedPath[PathAttribute::ABSOLUTE->value];

    if ($createDirectory) {
      $directory = pathinfo(
        $absolutePath,
        PathAttribute::pathinfoFlags(PathAttribute::DIRNAME)
      );
      if (!$this->createDirectory($directory)) {
        $this->errorCode = self::ERROR_CODE_DIR_CREATION_FAILED;
        return;
      }
    }

    if (!$overwrite && $this->fileAlreadyExists($absolutePath)) {
      $this->errorCode = self::ERROR_CODE_ALREDAY_EXISTS;
      return;
    }
    if (!$this->createFile($absolutePath, $contents, $append)) {
      $this->errorCode = self::ERROR_CODE_CREATION_FAILED;
      return;
    }

    $this->errorCode = self::ERROR_CODE_NO_ERROR;

  }

  public function getErrorCode(): int {
    return $this->errorCode;
  }

  public function getErrorMessage(): String {
    return match($this->errorCode) {
      self::ERROR_CODE_NO_ERROR            => self::ERROR_MESSAGE_NO_ERROR,
      self::ERROR_CODE_CREATION_FAILED     => self::ERROR_MESSAGE_CREATION_FAILED,
      self::ERROR_CODE_ALREDAY_EXISTS      => self::ERROR_MESSAGE_ALREADY_EXISTS,
      self::ERROR_CODE_DIR_CREATION_FAILED => self::ERROR_MESSAGE_DIR_CREATION_FAILED,
      self::ERROR_CODE_UNSUPPORTED_TYPE    => self::ERROR_MESSAGE_UNSUPPORTED_TYPE,
      self::ERROR_CODE_TOO_LARGE           => self::ERROR_MESSAGE_TOO_LARGE,
      default => self::ERROR_MESSAGE_UNDEFINED
    };
  }

  private function createFile(String $absolutePath, String $contents, bool $append): bool {

    $flag = LOCK_EX;
    if ($append) {
      $flag = $flag | FILE_APPEND;
    }

    return (file_put_contents($absolutePath, $contents, $flag) !== false);

  }

  private function createDirectory(String $directory): bool {
    return (is_dir($directory)) ? true : mkdir($directory, self::DIRECTORY_PERMISSIONS, true);
  }

  private function parsePath(String $path, String $rootDirectory): Array {

    $pathinfo = pathinfo($path, PathAttribute::pathinfoFlags());
    $pathinfo[PathAttribute::RELATIVE->value] = $path;
    $pathinfo[PathAttribute::ABSOLUTE->value] = sprintf("%s/%s", $rootDirectory, $path);
    $pathinfo[PathAttribute::EXTENSION->value] ??= "";

    return $pathinfo;

  }

  private function isTooLarge(int $size): bool {
    return ($size > self::MAX_FILE_SIZE);
  }

  private function isSupportedType(String $type): bool {
    return (MimeType::fromName($type) !== null);
  }

  private function fileAlreadyExists(String $absolutePath): bool {
    return (file_exists($absolutePath) && is_file($absolutePath));
  }

};

