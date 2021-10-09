<?php
namespace PyAngelo\Controllers\Tutorials;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;
use PyAngelo\Repositories\QuizRepository;

class TutorialsShowController extends Controller {
  protected $tutorialRepository;
  protected $quizRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    TutorialRepository $tutorialRepository,
    QuizRepository $quizRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->tutorialRepository = $tutorialRepository;
    $this->quizRepository = $quizRepository;
  }

  public function exec() {
    if (!isset($this->request->get['slug']))
      return $this->redirectToPageNotFound();

    if (!($tutorial = $this->tutorialRepository->getTutorialBySlugWithStats(
      $this->request->get['slug'],
      $this->auth->personId()
    )))
      return $this->redirectToPageNotFound();

    $lessons = $this->tutorialRepository->getTutorialLessons(
      $tutorial['tutorial_id'],
      $this->auth->personId()
    );
    $skills = $this->quizRepository->getTutorialSkillsMastery(
      $tutorial['tutorial_id'],
      $this->auth->personId()
    );
    $this->response->setView('tutorials/show.html.php');
    $this->response->setVars(array(
      'pageTitle' => $tutorial['title'] . ' | PyAngelo',
      'metaDescription' => $tutorial['description'],
      'activeLink' => 'Tutorials',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'tutorial' => $tutorial,
      'lessons' => $lessons,
      'skills' => $skills
    ));
    return $this->response;
  }

  private function redirectToPageNotFound() {
    $this->response->header('Location: /page-not-found');
    return $this->response;
  }
}
