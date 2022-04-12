<?php
namespace PyAngelo\Controllers\PasswordReset;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\FormServices\ForgotPasswordFormService;

class ForgotPasswordValidateController extends Controller {
  protected $forgotPasswordFormService;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    ForgotPasswordFormService $forgotPasswordFormService
  ) {
    parent::__construct($request, $response, $auth);
    $this->forgotPasswordFormService = $forgotPasswordFormService;
  }

  public function exec() {
    if ($this->auth->loggedIn())
      return $this->redirectToPasswordPage();

    if (!$this->auth->crsfTokenIsValid())
      return $this->redirectToForgotPasswordPage();

    if (! $this->forgotPasswordFormService->saveRequestAndSendEmail($this->request->post))
      return $this->redirectToForgotPasswordWithErrors();

    return $this->redirectToForgotPasswordConfirmPage();
  }

  private function redirectToPasswordPage() {
      $this->response->header('Location: /password');
      $this->flash('You are already logged in so you can simply change your password.', 'danger');
      return $this->response;
  }

  private function redirectToForgotPasswordPage() {
    $this->flash('Please request a password reset from the PyAngelo website.', 'danger');
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
    $location = 'Location: /forgot-password-confirm?email=' . urlencode($this->request->post['email']);
    $this->response->header($location);
    return $this->response;
  }
}
