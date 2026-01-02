<?php
namespace PyAngelo\Controllers\Registration;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\FormServices\RegisterFormService;
use Framework\Turnstile\TurnstileVerifier;

class RegisterValidateController extends Controller {
  protected $registerFormService;
  protected $turnstileVerifier;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    RegisterFormService $registerFormService,
    TurnstileVerifier $turnstileVerifier
  ) {
    parent::__construct($request, $response, $auth);
    $this->registerFormService = $registerFormService;
    $this->turnstileVerifier = $turnstileVerifier;
  }

  public function exec() {
    if ($this->auth->loggedIn()) 
      return $this->redirectToHomePage();

    if (! $this->auth->crsfTokenIsValid()) 
      return $this->redirectToRegisterPage();

    if ($this->formFilledInTooQuickly())
      return $this->logAttemptAndRedirectToRegisterPage();

    if (empty($this->request->post['cf-turnstile-response'])) {
      $this->flash('Cloudflare turnstile could not verify you were a human. Please try again.', 'warning');
      $_SESSION['formVars'] = $this->request->post;
      $this->response->header('Location: /register');
      return $this->response;
    }

    $token = $this->request->post['cf-turnstile-response'];
    $ip = $this->request->server['REMOTE_ADDR'];
    $result = $this->turnstileVerifier->verify($token, $ip);

    $isOk = $result['ok'] ?? false;
    if (!$isOk) {
      $this->logMessage('Turnstile failed: ' . json_encode([
        'errors' => $result['errors'] ?? [],
        'host'   => $result['host'] ?? null,
      ]), 'INFO');
      $this->flash('Cloudflare turnstile could not verify you were a human. Please try again.', 'warning');
      $_SESSION['formVars'] = $this->request->post;
      $this->response->header('Location: /register');
      return $this->response;
    }

    if (! $this->registerFormService->createPerson($this->request->post))
      return $this->redirectToRegisterPageAndShowErrors();

    return $this->redirectToConfirmEmailPage();
  }

  private function redirectToHomePage() {
      $this->flash('You are already logged in!', 'info');
      $this->response->header('Location: /');
      return $this->response;
  }

  private function redirectToRegisterPage() {
      $this->flash('Please register from the PyAngelo website!', 'danger');
      $this->response->header('Location: /register');
      return $this->response;
  }

  private function formFilledInTooQuickly() {
    if ((int)time() - (int)$this->request->post['time'] < 2)
      return true;
    else
      return false;
  }

  private function recaptchaInvalid() {
    if (empty($this->request->post['g-recaptcha-response'])) {
      $_SESSION['formVars'] = $this->request->post;
      return true;
    }
    $expectedRecaptchaAction = "registerwithversion3";
    if (!$this->recaptcha->verified(
      $this->request->server['SERVER_NAME'],
      $expectedRecaptchaAction,
      $this->request->post['g-recaptcha-response'],
      $this->request->server['REMOTE_ADDR']
    )) {
      $_SESSION['formVars'] = $this->request->post;
      return true;
    }
    return false;
  }

  private function logAttemptAndRedirectToRegisterPage() {
      $message = "User " . $this->request->post['givenName'] . " " . $this->request->post['familyName'] . " (" . $this->request->post['email'] . ") tried to register in less than 2 seconds\n";
      $this->logMessage($message, 'WARNING');
      $this->flash('Please register manually from the PyAngelo website!', 'danger');
      $this->response->header('Location: /register');
      return $this->response;
  }

  private function redirectToRegisterPageAndShowErrors() {
      $_SESSION['errors'] = $this->registerFormService->getErrors();
      $this->flash($this->registerFormService->getFlashMessage(), 'danger');
      $_SESSION['formVars'] = $this->request->post;
      $this->response->header('Location: /register');
      return $this->response;
  }

  private function redirectToConfirmEmailPage() {
    $location = 'Location: /please-confirm-your-registration?email=' . urlencode($this->request->post['email']);
    $this->response->header($location);
    return $this->response;
  }
}
