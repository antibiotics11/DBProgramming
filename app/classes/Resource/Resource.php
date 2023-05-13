<?php

namespace ContestApp\Resource;

class Resource {

  protected MimeType $type;
  protected String   $contents;

  public function __construct(?MimeType $type = null, String $contents = "") {

    if ($type === null) {
      $this->type = MimeType::_TXT;
    }
    $this->contents = $contents;

  }

  public function setMimeType(MimeType $type): void {
    $this->type = $type;
  }

  public function getMimeType(): MimeType {
    return $this->type;
  }

  public function setContents(String $contents): void {
    $this->contents = $contents;
  }

  public function getContents(): String {
    return $this->contents;
  }

};
