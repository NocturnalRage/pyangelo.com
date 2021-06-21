<?php
namespace PyAngelo\Controllers;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class HomePageController extends Controller {

  public function exec() {
    if ($this->auth->loggedIn())
      $this->response->setView('home-logged-in.html.php');
    else
      $this->response->setView('home.html.php');
    $this->setResponseInfo();
    return $this->response;
  }

  private function setResponseInfo() {
    $this->response->setVars(array(
      'pageTitle' => "PyAngelo - Learn To Code",
      'metaDescription' => "Python Graphics Programming in the Browser",
      'activeLink' => 'Home',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
    $this->addVar('flash');
  }
}
