<?php
namespace PyAngelo\Controllers\Registration;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class RegisterController extends Controller {
  public function exec() {
    if ($this->auth->loggedIn()) {
      return $this->redirectToHomePage();
    }

    $this->response->setView('registration/register.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Register for PyAngelo',
      'metaDescription' => "Register for PyAngelo and we'll teach you to program.",
      'activeLink' => 'Home',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));

    $this->includeAnyErrorsFormVarsAndFlash();

    return $this->response;
  }

  private function redirectToHomePage() {
      $this->flash('You are already logged in!', 'info');
      $this->response->header('Location: /');
      return $this->response;
  }

  private function includeAnyErrorsFormVarsAndFlash() {
    if (isset($this->request->session["errors"])) {
      $this->response->addVars(array(
        'errors' => $this->request->session["errors"]
      ));
      unset($this->request->session["errors"]);
    }

    if (isset($this->request->session["formVars"])) {
      $this->response->addVars(array(
        'formVars' => $this->request->session["formVars"]
      ));
      unset($this->request->session["formVars"]);
    }
    $this->addVar('flash');
  }
}
