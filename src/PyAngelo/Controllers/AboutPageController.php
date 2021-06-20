<?php
namespace PyAngelo\Controllers;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class AboutPageController extends Controller {
  public function exec() {
    $this->response->setView('about.html.php');
    $this->setResponseInfo();
    return $this->response;
  }

  private function setResponseInfo() {
    $this->response->setVars(array(
      'pageTitle' => "About PyAngelo",
      'metaDescription' => "My mission with PyAngelo is to teach everyone how to code.",
      'activeLink' => 'About',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
  }
}
