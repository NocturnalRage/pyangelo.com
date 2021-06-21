<?php
namespace PyAngelo\Controllers;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class LoginController extends Controller {
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
    if ($this->auth->loggedIn())
      return $this->redirectToHomePage();

    $this->response->setView('login.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'PyAngelo Login',
      'metaDescription' => "Login to the PyAngelo website.",
      'activeLink' => 'Home',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'recaptchaKey' => $this->recaptchaKey
    ));
    $this->addVar('errors');
    $this->addVar('formVars');
    $this->addVar('flash');
    return $this->response;
  }

  private function redirectToHomePage() {
    $this->flash('You are already logged in!', 'info');
    $this->response->header('Location: /');
    return $this->response;
  }
}
