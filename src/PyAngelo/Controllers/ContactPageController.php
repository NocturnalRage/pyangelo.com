<?php
namespace PyAngelo\Controllers;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class ContactPageController extends Controller {
  public function exec() {
    $this->response->setView('contact.html.php');
    $this->setResponseInfo();
    return $this->response;
  }

  private function setResponseInfo() {
    $this->response->setVars(array(
      'pageTitle' => "Contact Us",
      'metaDescription' => "Contact us if you want to know something about PyAngelo.",
      'activeLink' => 'Home',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
    $this->addVar('errors');
    $this->addVar('formVars');
    $this->addVar('flash');
  }
}
