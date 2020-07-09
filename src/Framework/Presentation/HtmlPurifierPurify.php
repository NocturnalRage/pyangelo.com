<?php
namespace Framework\Presentation;
use Framework\Contracts\PurifyContract;

class HtmlPurifierPurify implements PurifyContract {
  protected $htmlPurifier;

  public function __construct(\HTMLPurifier $htmlPurifier) {
    $this->htmlPurifier = $htmlPurifier;
  }
  public function purify($html) {
    return $this->htmlPurifier->purify($html);
  }
}
