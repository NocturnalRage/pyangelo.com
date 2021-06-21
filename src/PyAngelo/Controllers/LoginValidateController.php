<?php
namespace PyAngelo\Controllers;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use Framework\{Request, Response};
use Framework\Recaptcha\RecaptchaClient;

class LoginValidateController extends Controller {
  protected $recaptcha;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    RecaptchaClient $recaptcha
  ) {
    parent::__construct($request, $response, $auth);
    $this->recaptcha = $recaptcha;
  }

  public function exec() {
    if ($this->auth->loggedIn())
      return $this->redirectToHomePage();

    if (!$this->auth->crsfTokenIsValid())
      return $this->redirectToLoginPageWithCrsfWarning();

    if ($this->invalidEmailOrPassword())
      return $this->redirectToLoginPage();

    if ($this->recaptchaInvalid())
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

  private function invalidEmailOrPassword() {
    $invalid = false;
    if (empty($this->request->post['email'])) {
      $this->request->session['errors']['email'] = "You must enter your email to log in.";
      $invalid = true;
    }
    else if (filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL) === false) {
      $this->request->session['errors']['email'] = "You did not enter a valid email address.";
      $invalid = true;
    }
    
    if (empty($this->request->post['loginPassword'])) {
      $this->request->session['errors']['loginPassword'] = "You must enter a password to log in.";
      $invalid = true;
    }
    return $invalid;
  }

  private function recaptchaInvalid() {
    if (empty($this->request->post['g-recaptcha-response'])) {
      $this->flash('Login could not be validated. Please ensure you are a human!', 'danger');
      return true;
    }
    $expectedRecaptchaAction = "registerwithversion3";
    if (!$this->recaptcha->verified(
      $this->request->server['SERVER_NAME'],
      $expectedRecaptchaAction,
      $this->request->post['g-recaptcha-response'],
      $this->request->server['REMOTE_ADDR']
    )) {
      $this->flash('Login could not be checked. Please ensure you are a human!', 'danger');
      return true;
    }
    return false;
  }

  private function redirectToLoginPage() {
    $this->request->session['formVars'] = $this->request->post;
    $this->response->header("Location: /login");
    return $this->response;
  }
  private function redirectToLoginPageWithFailedLoginMessage() {
    $this->request->session['formVars'] = $this->request->post;
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
