<?php
namespace PyAngelo\Controllers;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use Framework\{Request, Response};

class LogoutController extends Controller {

  public function exec() {
    if (! $this->auth->loggedIn())
      return $this->redirectToHomePage();

    if (! $this->auth->crsfTokenIsValid())
      return $this->redirectToHomePageWithCrsfWarning();

    $this->logoutUser();

    $this->response->header('Location: /login');
    return $this->response;
  }

  private function logoutUser() {
    $this->auth->deleteRememberMe($this->auth->person()['person_id']);
    $this->deleteCookies();
    unset($_SESSION['loginEmail']);
    session_destroy();
  }

  private function deleteCookies() {
    $this->response->setcookie('rememberme', '', time()-3600, null, null, null, TRUE);
    $this->response->setcookie('remembermesession', '', time()-3600, null, null, null, TRUE);
    $this->response->setcookie('remembermetoken', '', time()-3600, null, null, null, TRUE);
  }

  private function redirectToHomePage() {
    $this->flash('You are already logged out!', 'info');
    $this->response->header('Location: /');
    return $this->response;
  }

  private function redirectToHomePageWithCrsfWarning() {
    $this->flash('You need to logout from the PyAngelo website!', 'danger');
    $this->response->header('Location: /');
    return $this->response;
  }
}
