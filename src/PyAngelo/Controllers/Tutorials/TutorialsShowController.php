<?php
namespace PyAngelo\Controllers\Tutorials;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;

class TutorialsShowController extends Controller {
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
    $this->response->setView('tutorials/show.html.php');
    $this->response->setVars(array(
      'pageTitle' => $tutorial['title'] . ' | PyAngelo',
      'metaDescription' => $tutorial['description'],
      'activeLink' => 'Tutorials',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'tutorial' => $tutorial,
      'lessons' => $lessons
    ));
    return $this->response;
  }

  private function redirectToPageNotFound() {
    $this->response->header('Location: /page-not-found');
    return $this->response;
  }
}
