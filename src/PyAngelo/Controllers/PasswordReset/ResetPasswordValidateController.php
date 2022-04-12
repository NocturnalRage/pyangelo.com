<?php
namespace PyAngelo\Controllers\PasswordReset;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\PersonRepository;
use Framework\{Request, Response};

class ResetPasswordValidateController extends Controller {
  protected $personRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    PersonRepository $personRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->personRepository = $personRepository;
  }

  public function exec() {
    if ($this->auth->loggedIn())
      return $this->redirectToPasswordPage();

    if (! $this->auth->crsfTokenIsValid())
      return $this->redirectToForgotPasswordPage();

    if (empty($this->request->post['token']))
      return $this->redirectToForgotPasswordPage();

    if (!$passwordRequest = $this->personRepository->getPasswordResetRequest($this->request->post['token']))
      return $this->redirectToForgotPasswordPage();
 
    if ($this->passwordDoesNotMeetRules())
      return $this->redirectToResetPasswordPage();

    $this->updatePassword($passwordRequest['person_id']);

    $this->flash('Your password has been reset. Please log in.', 'success');
    $this->response->header("Location: /login");
    return $this->response;
  }

  private function redirectToPasswordPage() {
    $this->response->header('Location: /password');
    $this->flash('You are already logged in so you can simply change your password.', 'danger');
    return $this->response;
  }

  private function redirectToForgotPasswordPage() {
    $this->flash('Something seems wrong here. Can you please restart the process to reset your password.', 'danger');
    $this->response->header('Location: /forgot-password');
    return $this->response;
  }

  private function passwordDoesNotMeetRules() {
    if (empty($this->request->post['loginPassword'])) {
      $_SESSION['errors']['loginPassword'] = "You must supply a password in order to reset it.";
      return true;
    }
    $loginPassword = $this->request->post['loginPassword'];
    if (strlen($loginPassword) < 4 || strlen($loginPassword) > 30) {
      $_SESSION['errors']['loginPassword'] = "The password must be between 4 characters and 30 characters long.";
      return true;
    }
    return false;
  }

  private function redirectToResetPasswordPage() {
    $redirectLocation = 'Location: /reset-password?token=' . urlencode($this->request->post['token']);
    $this->response->header($redirectLocation);
    return $this->response;
  }

  private function updatePassword($personId) {
    $this->personRepository->updatePassword(
      $personId,
      $this->request->post['loginPassword']
    );
    $this->personRepository->processPasswordResetRequest(
      $personId,
      $this->request->post['token']
    );
  }
}
