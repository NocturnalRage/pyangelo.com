<?php
namespace PyAngelo\Controllers\Registration;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\FormServices\RegisterFormService;

class RegisterValidateController extends Controller {
  protected $registerFormService;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    RegisterFormService $registerFormService
  ) {
    parent::__construct($request, $response, $auth);
    $this->registerFormService = $registerFormService;
  }

  public function exec() {
    if ($this->auth->loggedIn()) 
      return $this->redirectToHomePage();

    if (! $this->auth->crsfTokenIsValid()) 
      return $this->redirectToRegisterPage();

    if ($this->formFilledInTooQuickly())
      return $this->logAttemptAndRedirectToRegisterPage();

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
    if ((int)time() - (int)$this->request->post['time'] < 3)
      return true;
    else
      return false;
  }

  private function logAttemptAndRedirectToRegisterPage() {
      $message = "User " . $this->request->post['givenName'] . " " . $this->request->post['familyName'] . " (" . $this->request->post['email'] . ") tried to register in less than 3 seconds\n";
      file_put_contents($_ENV['APPLICATION_LOG_FILE'], $message, FILE_APPEND);
      $this->flash('Please register manually from the PyAngelo website!', 'danger');
      $this->response->header('Location: /register');
      return $this->response;
  }

  private function redirectToRegisterPageAndShowErrors() {
      $this->request->session['errors'] = $this->registerFormService->getErrors();
      $this->flash($this->registerFormService->getFlashMessage(), 'danger');
      $this->request->session['formVars'] = $this->request->post;
      $this->response->header('Location: /register');
      return $this->response;
  }

  private function redirectToConfirmEmailPage() {
    $location = 'Location: /please-confirm-your-registration?email=' . urlencode($this->request->post['email']);
    $this->response->header($location);
    return $this->response;
  }
}
