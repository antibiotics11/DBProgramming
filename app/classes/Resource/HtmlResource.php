<?php

namespace ContestApp\Resource;

class HtmlResource extends Resource {

  public const DOCUMENT_FORM = "<!DOCTYPE html><html lang=\"%s\"><head>%s</head><body>%s</body></html>";
  public const DOCUMENT_DEFAULT_LANGUAGE = "en-US";


  private Array   $head;
  private String  $body;

  private String  $language;

  public function __construct() {

    $this->setLanguage(self::DOCUMENT_DEFAULT_LANGUAGE);
    $this->type = MimeType::_HTML;
    $this->pushHead("<meta http-equiv=\"content-type\" content=\"text/html\">");
    $this->pushHead("<meta charset=\"utf-8\">");

  }

  public function pushHead(String $data): void {
    $this->head[] = $data;
  }

  public function setHead(Array $head): void {
    $this->head = $head;
  }

  public function getHead(): Array {
    return $this->head;
  }

  public function setBody(String $body): void {
    $this->body = $body;
  }

  public function getBody(): String {
    return $this->body;
  }

  public function setLanguage(String $language = self::DOCUMENT_DEFAULT_LANGUAGE): void {
    $this->language = $language;
  }

  public function getLanguage(): String {
    return $this->language;
  }

  public function pack(): String {

    $this->setContents(preg_replace("/\t/", "",
      sprintf(self::DOCUMENT_FORM,
        $this->language,
        implode("\r\n", $this->head),
        $this->body
      )
    ));

    return $this->getContents();

  }

};

