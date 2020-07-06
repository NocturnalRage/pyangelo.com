<?php
namespace PyAngelo\Controllers\PasswordReset;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class ForgotPasswordController extends Controller {
  public function exec() {
    if ($this->auth->loggedIn())
      return $this->redirectToHomePage();

    $this->response->setView('password-reset/forgot-password.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Forgot Password',
      'metaDescription' => "You've forgotton your password. No problems we can reset it for you.",
      'activeLink' => 'Home',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
    $this->addVar('errors');
    $this->addVar('formVars');
    $this->addVar('flash');
    return $this->response;
  }

  private function redirectToHomePage() {
    $this->flash('You are already logged in!', 'danger');
    $this->response->header('Location: /');
    return $this->response;
  }
}
