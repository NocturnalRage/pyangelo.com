<?php
namespace PyAngelo\Controllers\Registration;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class RegisterConfirmController extends Controller {
  public function exec() {
    if ($this->auth->loggedIn()) {
      return $this->redirectToHomePage();
    }

    $registeredEmail = isset($this->request->get['email']) ? $this->request->get['email'] : 'the address you entered';

    $this->response->setView('registration/please-confirm-your-registration.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Confirm Your Email Address',
      'metaDescription' => "Please confirm your email address and then you'll be a free member of the PyAngelo website.",
      'activeLink' => 'Home',
      'registeredEmail' => $registeredEmail,
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
    return $this->response;
  }

  private function redirectToHomePage() {
      $this->flash('You are already logged in!', 'info');
      $this->response->header('Location: /');
      return $this->response;
  }
}
