<?php
namespace PyAngelo\Controllers\Lessons;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;
use Framework\{Request, Response};

class LessonsToggleFavouritedController extends Controller {
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
    $this->response->setView('lessons/toggle-favourited.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Log in to record your progress'
      ));
      return $this->response;
    }

    if (empty($this->request->post['lessonId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a lesson to favourite.'
      ));
      return $this->response;
    }

    if (! $lesson = $this->tutorialRepository->getLessonById($this->request->post['lessonId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a valid lesson to favourite.'
      ));
      return $this->response;
    }

    $lessonFavourited = $this->tutorialRepository->getLessonFavourited(
      $this->auth->personId(),
      $this->request->post['lessonId']
    );

    if (! $lessonFavourited) {
      $this->tutorialRepository->insertLessonFavourited(
        $this->auth->personId(),
        $this->request->post['lessonId']
      );
      $this->response->setVars(array(
        'status' => 'success',
        'message' => 'Lesson added to favourites.'
      ));
    }
    else {
      $this->tutorialRepository->deleteLessonFavourited(
        $this->auth->personId(),
        $this->request->post['lessonId']
      );
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Lesson removed from favourites.'
      ));
    }
    return $this->response;
  }
}
