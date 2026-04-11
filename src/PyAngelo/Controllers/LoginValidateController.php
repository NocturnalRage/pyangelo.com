<?php
namespace PyAngelo\Controllers;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use Framework\Turnstile\TurnstileVerifier;
use Framework\{Request, Response};

class LoginValidateController extends Controller {
  protected $turnstileVerifier;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    TurnstileVerifier $turnstileVerifier
  ) {
    parent::__construct($request, $response, $auth);
    $this->turnstileVerifier = $turnstileVerifier;
  }

  public function exec() {
    if ($this->auth->loggedIn())
      return $this->redirectToHomePage();

    if (!$this->auth->crsfTokenIsValid())
      return $this->redirectToLoginPageWithCrsfWarning();

    if ($this->turnstileInvalid())
      return $this->redirectToLoginPageWithTurnstileWarning();

    if ($this->invalidEmailOrPassword())
      return $this->redirectToLoginPage();

    if (!$this->auth->authenticateLogin($this->request->post['email'], $this->request->post['loginPassword']))
      return $this->redirectToLoginPageWithFailedLoginMessage();

    if (isset($this->request->post['rememberme']))
      $this->setRememberMeCookies();

    $this->flash('You are now logged in', 'success');
    if (isset($_SESSION['redirect']) && $this->isSafeRedirect($_SESSION['redirect']))
      $this->response->header("Location: ". $_SESSION['redirect']);
    else
      $this->response->header("Location: /");

    return $this->response;
  }

  private function redirectToHomePage() {
    $this->flash('You are already logged in!', 'warning');
    $this->response->header('Location: /');
    return $this->response;
  }

  private function turnstileInvalid() {
    if (empty($this->request->post['cf-turnstile-response'])) {
      return true;
    }
    $token = $this->request->post['cf-turnstile-response'];
    $ip = $this->request->server['REMOTE_ADDR'] ?? null;
    $result = $this->turnstileVerifier->verify($token, $ip);
    return !($result['ok'] ?? false);
  }

  private function redirectToLoginPageWithTurnstileWarning() {
    $this->flash('Cloudflare turnstile could not verify you were a human. Please try again.', 'warning');
    $_SESSION['formVars'] = $this->request->post;
    $this->response->header('Location: /login');
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
      $_SESSION['errors']['email'] = "You must enter your email to log in.";
      $invalid = true;
    }
    else if (filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL) === false) {
      $_SESSION['errors']['email'] = "You did not enter a valid email address.";
      $invalid = true;
    }
    
    if (empty($this->request->post['loginPassword'])) {
      $_SESSION['errors']['loginPassword'] = "You must enter a password to log in.";
      $invalid = true;
    }
    return $invalid;
  }

  private function redirectToLoginPage() {
    $_SESSION['formVars'] = $this->request->post;
    $this->response->header("Location: /login");
    return $this->response;
  }
  private function redirectToLoginPageWithFailedLoginMessage() {
    $_SESSION['formVars'] = $this->request->post;
    $this->flash('The email and password do not match. Login failed.', 'danger');
    $this->response->header("Location: /login");
    return $this->response;
  }

  private function isSafeRedirect($url) {
    // Only allow relative paths — reject anything with a scheme or protocol-relative URLs
    return strpos($url, '/') === 0 && strpos($url, '//') !== 0;
  }

  private function setRememberMeCookies() {
    if ($this->request->post['rememberme'] == 'y') {
      $personId = $this->auth->person()['person_id'];
      $session = bin2hex(random_bytes(32));
      $token = bin2hex(random_bytes(32));
      $tokenHash = password_hash($token, PASSWORD_DEFAULT);
      $this->auth->insertRememberMe($personId, $session, $tokenHash);
      $cookieOptions = [
        'expires'  => time() + 60*60*24*365,
        'path'     => '/',
        'secure'   => true,
        'httponly' => true,
        'samesite' => 'Lax',
      ];
      $this->response->setcookie('rememberme', $personId, $cookieOptions);
      $this->response->setcookie('remembermesession', $session, $cookieOptions);
      $this->response->setcookie('remembermetoken', $token, $cookieOptions);
    }
  }
}
