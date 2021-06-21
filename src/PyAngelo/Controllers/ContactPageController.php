<?php
namespace PyAngelo\Controllers;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class ContactPageController extends Controller {
  protected $recaptchaKey;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    $recaptchaKey
  ) {
    parent::__construct($request, $response, $auth);
    $this->recaptchaKey = $recaptchaKey;
  }

  public function exec() {
    $this->response->setView('contact.html.php');
    $this->setResponseInfo();
    return $this->response;
  }

  private function setResponseInfo() {
    $this->response->setVars(array(
      'pageTitle' => "PyAngelo - Learn To Program",
      'metaDescription' => "Python Graphics Programming in the Browser",
      'activeLink' => 'Home',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'recaptchaKey' => $this->recaptchaKey
    ));
    $this->addVar('errors');
    $this->addVar('formVars');
    $this->addVar('flash');
  }
}
