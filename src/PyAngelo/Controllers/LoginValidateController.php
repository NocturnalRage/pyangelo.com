<?php
namespace PyAngelo\Controllers;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use Framework\{Request, Response};

class LoginValidateController extends Controller {
  public function exec() {
    if ($this->auth->loggedIn())
      return $this->redirectToHomePage();

    if (!$this->auth->crsfTokenIsValid())
      return $this->redirectToLoginPageWithCrsfWarning();

    if ($this->missingEmailOrPassword())
      return $this->redirectToLoginPage();

    if (!$this->auth->authenticateLogin($this->request->post['email'], $this->request->post['loginPassword']))
      return $this->redirectToLoginPageWithFailedLoginMessage();

    if (isset($this->request->post['rememberme']))
      $this->setRememberMeCookies();

    $this->flash('You are now logged in', 'success');
    if (isset($this->request->session['redirect']))
      $this->response->header("Location: ". $this->request->session['redirect']);
    else 
      $this->response->header("Location: /");

    return $this->response;
  }

  private function redirectToHomePage() {
    $this->flash('You are already logged in!', 'warning');
    $this->response->header('Location: /');
    return $this->response;
  }

  private function redirectToLoginPageWithCrsfWarning() {
    $this->flash('Please log in from the PyAngelo website.', 'danger');
    $this->response->header('Location: /login');
    return $this->response;
  }

  private function missingEmailOrPassword() {
    $missing = false;
    if (empty($this->request->post['email'])) {
      $this->request->session['errors']['email'] = "You must enter your email to log in.";
      $missing = true;
    }
    
    if (empty($this->request->post['loginPassword'])) {
      $this->request->session['errors']['loginPassword'] = "You must enter a password to log in.";
      $missing = true;
    }
    return $missing;
  }
  private function redirectToLoginPage() {
    $this->response->header("Location: /login");
    return $this->response;
  }
  private function redirectToLoginPageWithFailedLoginMessage() {
    $this->flash('The email and password do not match. Login failed.', 'danger');
    $this->response->header("Location: /login");
    return $this->response;
  }

  private function setRememberMeCookies() {
    if ($this->request->post['rememberme'] == 'y') {
      $personId = $this->auth->person()['person_id'];
      $session = bin2hex(openssl_random_pseudo_bytes(32));
      $token = bin2hex(openssl_random_pseudo_bytes(32));
      $tokenHash = password_hash($token, PASSWORD_DEFAULT);
      $this->auth->insertRememberMe($personId, $session, $tokenHash);
      $this->response->setcookie('rememberme', $personId, time()+60*60*24*365, null, null, null, TRUE);
      $this->response->setcookie('remembermesession', $session, time()+60*60*24*365, null, null, null, TRUE);
      $this->response->setcookie('remembermetoken', $token, time()+60*60*24*365, null, null, null, TRUE);
    }
  }
}
