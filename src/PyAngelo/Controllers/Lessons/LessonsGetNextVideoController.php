<?php
namespace PyAngelo\Controllers\Lessons;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;

class LessonsGetNextVideoController extends Controller {
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
    $this->response->setView('lessons/next-video.json.php');
    $this->response->header('Content-Type: application/json');

    if (! isset($this->request->get['tutorialId']) ||
        ! isset($this->request->get['displayOrder'])
    ) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'Invalid next video request.',
        'lessonTitle' => 'none',
        'tutorialSlug' => 'none',
        'lessonSlug' => 'none',
      ));
      return $this->response;
    }

    if (! $lesson = $this->getNextLesson()) {
      $this->response->setVars(array(
        'status' => 'completed',
        'message' => 'Last video in tutorial.',
        'lessonTitle' => 'none',
        'tutorialSlug' => 'none',
        'lessonSlug' => 'none',
      ));
      return $this->response;
    }

    $this->response->setVars(array(
      'status' => 'success',
      'message' => 'Next video retrieved.',
      'lessonTitle' => $lesson['lesson_title'],
      'tutorialSlug' => $lesson['tutorial_slug'],
      'lessonSlug' =>$lesson['lesson_slug']
    ));
    return $this->response;
  }

  private function getNextLesson() {
    return $this->tutorialRepository->getNextLessonInTutorial(
      $this->request->get['tutorialId'],
      $this->request->get['displayOrder']
    );
  }
}
