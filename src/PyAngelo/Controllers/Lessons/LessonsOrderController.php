<?php
namespace PyAngelo\Controllers\Lessons;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;

class LessonsOrderController extends Controller {
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
    if (!$this->auth->isAdmin()) {
      $status = 'error';
      $message = 'You are not authorised!';
    }
    elseif (empty($this->request->post['slug'])) {
      $status = 'error';
      $message = 'The tutorial was not specified!';
    }
    elseif (! $tutorial = $this->tutorialRepository->getTutorialBySlug($this->request->post['slug'])) {
      $status = 'error';
      $message = 'The tutorial specified does not exist!';
    }
    elseif (!isset($this->request->post['idsInOrder'])) {
      $status = 'error';
      $message = 'The order of the lessons was not received!';
    }
    else {
      $position = 0;
      foreach ($this->request->post['idsInOrder'] as $lessonSlug) {
        $position++;
        $this->tutorialRepository->updateLessonOrder(
          $tutorial['tutorial_id'],
          $lessonSlug,
          $position
        );
      }
      $status = 'success';
      $message = 'The new order has been saved.';
    }

    $this->response->setView('lessons/order.json.php');
    $this->response->header('Content-Type: application/json');
    $this->response->setVars(array(
      'status' => $status,
      'message' => $message
    ));
    return $this->response;
  }
}
