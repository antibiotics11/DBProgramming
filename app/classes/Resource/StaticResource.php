<?php

namespace ContestApp\Resource;

class StaticResource extends Resource {

  private const COMMON_HASH_TYPES = [ "crc32", "md5", "sha1", "sha256" ];

  private String $path;

  private Array  $hash;

  private int $lastAccessed;
  private int $lastModified;

  private function setPath(String $path): void {

    $path = realpath($path);
    if ($path === false) {
      throw new \InvalidArgumentException("Specified path does not exist.");
    }
    if (is_dir($path)) {
      throw new \InvalidArgumentException(
        sprintf("Specified path %s is not file.", $path)
      );
    }

    $this->path = $path;

  }

  private function setType(String $type): void {

    $type = MimeType::fromName($type) ?? MimeType::_TXT;
    $this->setMimeType($type);

  }

  private function setHash(): void {

    foreach (self::COMMON_HASH_TYPES as $algo) {
      $this->hash[$algo] = hash($algo, $this->contents);
    }

  }

  private function setTimeInfo(): void {

    $this->lastAccessed = fileatime($this->path);
    $this->lastModified = filemtime($this->path);

  }

  public function __construct(String $path = "") {

    if (strlen($path) > 1) {
      $this->setNewResource($path);
    }

  }

  public function setNewResource(String $path): void {

    $this->setPath($path);
    $pathinfo = pathinfo($path, PathAttribute::pathinfoFlags());

    $pathinfo[PathAttribute::EXTENSION->value] ??= "txt";
    $this->setType($pathinfo[PathAttribute::EXTENSION->value]);

    $contents = file_get_contents($this->path);
    if ($contents === false) {
      $contents = "";
    }
    $this->contents = $contents;

    $this->setHash();
    $this->setTimeInfo();

  }

  public function getPath(): String {
    return $this->path;
  }

  public function getHash(): Array {
    return $this->hash;
  }

  public function getLastAccessed(): int {
    return $this->lastAccessed;
  }

  public function getLastModified(): int {
    return $this->lastModified;
  }

};

