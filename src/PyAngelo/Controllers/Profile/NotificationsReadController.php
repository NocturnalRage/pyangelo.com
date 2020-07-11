<?php
namespace PyAngelo\Controllers\Profile;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\PersonRepository;

class NotificationsReadController extends Controller {
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
    $this->response->setView('profile/notification-read.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must be logged in to mark a notification as read.'
      ));
      return $this->response;
    }

    if (! $this->auth->crsfTokenIsValid()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must perform this action from the PyAngelo website.'
      ));
      return $this->response;
    }

    if (empty($this->request->post['notificationId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a notification to mark as read.'
      ));
      return $this->response;
    }

    if (! ($notification = $this->personRepository->getNotificationById($this->auth->personId(), $this->request->post['notificationId']))) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a valid notification to mark as read.'
      ));
      return $this->response;
    }

    $this->personRepository->markNotificationAsRead(
      $this->auth->personId(),
      $this->request->post['notificationId']
    );

    $this->response->setVars(array(
      'status' => 'success',
      'message' => 'The notification has been marked as read.'
    ));
    return $this->response;
  }
}
