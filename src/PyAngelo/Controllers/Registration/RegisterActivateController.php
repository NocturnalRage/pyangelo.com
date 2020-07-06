<?php
namespace PyAngelo\Controllers\Registration;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\PersonRepository;

class RegisterActivateController extends Controller {
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
      return $this->redirectToHomePage();

    if ($this->noTokenReceived())
      return $this->redirectToRegisterPageWithNoTokenMessage();

    if (! $this->tokenProcessed()) {
      return $this->redirectToRegisterPageWithCouldNotProcessMessage();
    }

    $this->response->header('Location: /thanks-for-registering');
    return $this->response;
  }

  private function tokenProcessed() {
    $membershipActivate = $this->personRepository->getMembershipActivate(
      $this->request->get['token']
    );
    if (!$membershipActivate) {
      return false;
    }
    $rowsUpdated = $this->personRepository->processMembershipActivate(
      $this->request->get['token']
    );
    if ($rowsUpdated != 1) {
      return false;
    }
    $this->personRepository->makeActive($membershipActivate['person_id']);

    $this->request->session['loginEmail'] = $membershipActivate['email'];
    $this->auth->setLoginStatus();

    $this->subscribeToNewsletter($membershipActivate['person_id']);
    return true;
  }

  private function subscribeToNewsletter($personId) {
    $listId = 1;
    $activeStatus = 1;
    $subscriber = $this->personRepository->getSubscriber($listId, $personId);
    if (!$subscriber) {
      $this->personRepository->insertSubscriber($listId, $personId);
    }
    else {
      if ($subscriber['subscriber_status_id'] != 1) {
        $this->personRepository->updateSubscriber(
          $listId,
          $personId,
          $activeStatus
        );
      }
    }
  }

  private function redirectToHomePage() {
    $this->flash('You are already logged in!', 'danger');
    $this->response->header('Location: /');
    return $this->response;
  }

  private function noTokenReceived() {
    if (isset($this->request->get['token']))
      return false;
    else
      return true;
  } 

  private function redirectToRegisterPageWithNoTokenMessage() {
      $this->flash('We could not activate your free membership. Please start the registration process again. Your registration token was missing.', 'danger');
      $this->response->header('Location: /register');
      return $this->response;
  }

  private function redirectToRegisterPageWithCouldNotProcessMessage() {
    $this->flash('We could not activate your free membership. Please start the registration process again.', 'danger');
    $this->response->header('Location: /register');
    return $this->response;
  }
}
