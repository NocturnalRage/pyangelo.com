<?php
namespace PyAngelo\Controllers;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class TermsController extends Controller {
  public function exec() {
    $this->response->setView('terms-of-use.html.php');
    $this->setResponseInfo();
    return $this->response;
  }

  private function setResponseInfo() {
    $this->response->setVars(array(
      'pageTitle' => "Terms of Use | PyAngelo",
      'metaDescription' => "The PyAngelo Terms of Use.",
      'activeLink' => 'Terms',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
  }
}
