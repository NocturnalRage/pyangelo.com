<?php
namespace PyAngelo\Controllers\Lessons;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;
use Framework\{Request, Response};

class LessonsToggleAlertController extends Controller {
  protected $tutorialRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    TutorialRepository $tutorialRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->tutorialRepository = $tutorialRepository;
  }

  public function exec() {
    $this->response->setView('lessons/toggle-alert.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Log in to update your notifications'
      ));
      return $this->response;
    }

    // Is the CRSF Token Valid
    if (! $this->auth->crsfTokenIsValid()) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('Please update your notifications from the PyAngelo website.')
      ));
      return $this->response;
    }

    if (empty($this->request->post['lessonId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a lesson to be notified about.'
      ));
      return $this->response;
    }

    if (! $blog = $this->tutorialRepository->getLessonById($this->request->post['lessonId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a valid lesson to be notified about.'
      ));
      return $this->response;
    }

    $alertUser = $this->tutorialRepository->shouldUserReceiveAlert(
      $this->request->post['lessonId'],
      $this->auth->personId()
    );

    if (! $alertUser) {
      $this->tutorialRepository->addToLessonAlert(
        $this->request->post['lessonId'],
        $this->auth->personId()
      );
      $this->response->setVars(array(
        'status' => 'success',
        'message' => 'Notifications are on for this lesson'
      ));
    }
    else {
      $this->tutorialRepository->removeFromLessonAlert(
        $this->request->post['lessonId'],
        $this->auth->personId()
      );
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Notifications are off for this lesson'
      ));
    }
    return $this->response;
  }
}
