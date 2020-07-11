<?php
namespace PyAngelo\Controllers\Lessons;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;

class LessonsSortController extends Controller {
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
      $this->flash('You are not authorised!', 'danger');
      $this->response->header('Location: /');
      return $this->response;
    }

    if (! $tutorial = $this->getTutorial()) {
      $this->response->header('Location: /page-not-found');
      return $this->response;
    }

    $lessons = $this->tutorialRepository->getTutorialLessons(
      $tutorial['tutorial_id'],
      $this->auth->person()['person_id']
    );

    $this->response->setView('lessons/sort.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Sort PyAngelo Lessons',
      'metaDescription' => "A page where you can change the order PyAngelo lessons are displayed for a tutorial.",
      'activeLink' => 'Tutorials',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'tutorial' => $tutorial,
      'lessons' => $lessons
    ));
    return $this->response;
  }

  private function getTutorial() {
    if (!isset($this->request->get['slug'])) {
      return false;
    }
    if (!($tutorial = $this->tutorialRepository->getTutorialBySlug($this->request->get['slug']))) {
      return false;
    }
    return $tutorial;
  }
}
