<?php
namespace PyAngelo\Controllers\Lessons;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;
use Framework\{Request, Response};

class LessonsToggleCompletedController extends Controller {
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
    $this->response->setView('lessons/toggle-completed.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Log in to record your progress',
        'percentComplete' => 0
      ));
      return $this->response;
    }

    if (empty($this->request->post['lessonId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must watch a lesson to complete it.',
        'percentComplete' => 0
      ));
      return $this->response;
    }

    if (! $lesson = $this->tutorialRepository->getLessonById($this->request->post['lessonId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a valid lesson to complete.',
        'percentComplete' => 0
      ));
      return $this->response;
    }

    $lessonComplete = $this->tutorialRepository->getLessonCompleted(
      $this->auth->personId(),
      $this->request->post['lessonId']
    );

    if (! $lessonComplete) {
      $this->tutorialRepository->insertLessonCompleted(
        $this->auth->personId(),
        $this->request->post['lessonId']
      );
      $this->response->setVars(array(
        'status' => 'success',
        'message' => 'Lesson marked as completed.'
      ));
    }
    else if ($this->request->post['action'] == 'complete') {
      $this->response->setVars(array(
        'status' => 'success',
        'message' => 'Lesson marked as completed.'
      ));
    }
    else if ($this->request->post['action'] == 'toggle') {
      $this->tutorialRepository->deleteLessonCompleted(
        $this->auth->personId(),
        $this->request->post['lessonId']
      );
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Lesson marked as incomplete.'
      ));
    }
    else {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'We could not record your action.'
      ));
    }
    $percentComplete = $this->tutorialRepository->getTutorialPercentComplete(
      $this->auth->personId(),
      $this->request->post['lessonId']
    );
    $this->response->addVars(array(
      'percentComplete' => $percentComplete
    ));
    return $this->response;
  }
}
