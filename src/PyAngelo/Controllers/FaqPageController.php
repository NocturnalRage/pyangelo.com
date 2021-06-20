<?php
namespace PyAngelo\Controllers;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class FaqPageController extends Controller {
  public function exec() {
    $this->response->setView('faq.html.php');
    $this->setResponseInfo();
    return $this->response;
  }

  private function setResponseInfo() {
    $this->response->setVars(array(
      'pageTitle' => "FAQ | PyAngelo",
      'metaDescription' => "Frequently Asked Questions about the PyAngelo website.",
      'activeLink' => 'FAQ',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
  }
}
