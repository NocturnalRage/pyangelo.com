<?php
namespace PyAngelo\Controllers\PasswordReset;

use Framework\{Request, Response};
use Framework\Turnstile\TurnstileVerifier;
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\FormServices\ForgotPasswordFormService;

class ForgotPasswordValidateController extends Controller {
  protected $forgotPasswordFormService;
  protected $turnstileVerifier;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    ForgotPasswordFormService $forgotPasswordFormService,
    TurnstileVerifier $turnstileVerifier
  ) {
    parent::__construct($request, $response, $auth);
    $this->forgotPasswordFormService = $forgotPasswordFormService;
    $this->turnstileVerifier = $turnstileVerifier;
  }

  public function exec() {
    if ($this->auth->loggedIn())
      return $this->redirectToPasswordPage();

    if (!$this->auth->crsfTokenIsValid())
      return $this->redirectToForgotPasswordPage();

    if ($this->turnstileInvalid())
      return $this->redirectToForgotPasswordPageWithTurnstileWarning();

    if (! $this->forgotPasswordFormService->saveRequestAndSendEmail($this->request->post))
      return $this->redirectToForgotPasswordWithErrors();

    return $this->redirectToForgotPasswordConfirmPage();
  }

  private function redirectToPasswordPage() {
      $this->response->header('Location: /password');
      $this->flash('You are already logged in so you can simply change your password.', 'danger');
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

  private function redirectToForgotPasswordPage() {
    $this->flash('Please request a password reset from the PyAngelo website.', 'danger');
    $this->response->header('Location: /forgot-password');
    return $this->response;
  }

  private function redirectToForgotPasswordPageWithTurnstileWarning() {
    $this->flash('Cloudflare turnstile could not verify you were a human. Please try again.', 'warning');
    $_SESSION['formVars'] = $this->request->post;
    $this->response->header('Location: /forgot-password');
    return $this->response;
  }

  private function redirectToForgotPasswordWithErrors() {
    $_SESSION['errors'] = $this->forgotPasswordFormService->getErrors();
    $this->flash($this->forgotPasswordFormService->getFlashMessage(), 'danger');
    $_SESSION['formVars'] = $this->request->post;
    $this->response->header('Location: /forgot-password');
    return $this->response;
  }

  private function redirectToForgotPasswordConfirmPage() {
    $_SESSION['forgotPasswordRequestSent'] = true;
    $this->response->header('Location: /forgot-password-confirm');
    return $this->response;
  }
}
