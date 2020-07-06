<?php
namespace PyAngelo\Controllers;
use PyAngelo\Controllers\Controller;
use Framework\{Request, Response};
use PyAngelo\Auth\Auth;

class PageNotFoundController extends Controller {
  public function exec() {
    $this->response->setView('page-not-found.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'PyAngelo | Page Not Found',
      'metaDescription' => 'Whoops! We could not find the page your were looking for.',
      'activeLink' => 'Home',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
    return $this->response;
  }
}
