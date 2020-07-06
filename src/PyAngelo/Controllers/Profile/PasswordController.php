<?php
namespace PyAngelo\Controllers\Profile;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class PasswordController extends Controller {
  public function exec() {
    if (! $this->auth->loggedIn())
      return $this->redirectToLoginPage();

    $this->response->setView('profile/password.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Change My Password',
      'metaDescription' => 'Change your PyAngelo password.',
      'activeLink' => 'password',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
    $this->addVar('errors');
    return $this->response;
  }

  private function redirectToLoginPage() {
    $this->flash('You must be logged in to change your password.', 'danger');
    $this->response->header('Location: /login');
    return $this->response;
  }
}
