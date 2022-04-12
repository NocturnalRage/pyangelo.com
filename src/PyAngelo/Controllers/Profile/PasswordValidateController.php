<?php
namespace PyAngelo\Controllers\Profile;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\PersonRepository;
use Framework\{Request, Response};

class PasswordValidateController extends Controller {
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
    if (! $this->auth->loggedIn())
      return $this->redirectToLoginPage();

    if (! $this->auth->crsfTokenIsValid())
      return $this->redirectToPasswordPage();

    if ($this->passwordDoesNotMeetRules())
      return $this->redirectToPasswordPageWithErrorMessage();

    $this->personRepository->updatePassword(
      $this->auth->personId(),
      $this->request->post['loginPassword']
    );

    $this->flash('Your password has been reset.', 'success');
    $this->response->header("Location: /profile");
    return $this->response;
  }

  private function redirectToLoginPage() {
    $this->flash('You must be logged in to change your password.', 'danger');
    $this->response->header('Location: /login');
    return $this->response;
  }

  private function redirectToPasswordPage() {
    $this->flash('Please update your password from the PyAngelo website.', 'danger');
    $this->response->header('Location: /password');
    return $this->response;
  }

  private function passwordDoesNotMeetRules() {
    if (empty($this->request->post['loginPassword'])) {
      $_SESSION['errors']['loginPassword'] = "You must supply a password in order to change it.";
      return true;
    }
    $loginPassword = $this->request->post['loginPassword'];
    if (strlen($loginPassword) < 4 || strlen($loginPassword) > 30) {
      $_SESSION['errors']['loginPassword'] = "The password must be between 4 characters and 30 characters long.";
      return true;
    }
    return false;
  }

  private function redirectToPasswordPageWithErrorMessage() {
    $this->response->header('Location: /password');
    return $this->response;
  }
}
