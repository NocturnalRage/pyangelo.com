<?php
namespace PyAngelo\Controllers\Lessons;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;
use PyAngelo\Repositories\SketchRepository;

class LessonsNewController extends Controller {
  protected $tutorialRepository;
  protected $sketchRepository;
  protected $ownerOfStarterSketchesId;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    TutorialRepository $tutorialRepository,
    SketchRepository $sketchRepository,
    $ownerOfStarterSketchesId
  ) {
    parent::__construct($request, $response, $auth);
    $this->tutorialRepository = $tutorialRepository;
    $this->sketchRepository = $sketchRepository;
    $this->ownerOfStarterSketchesId = $ownerOfStarterSketchesId;
  }

  public function exec() {
    if (!$this->auth->isAdmin())
      return $this->redirectToHomePageWithWarning();

    if (! $tutorial = $this->getTutorial())
      return $this->redirectToPageNotFound();

    $this->response->setView('lessons/new.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Create a New Lesson for ' . $tutorial['title'],
      'metaDescription' => 'Create a new lesson as part of the ' . $tutorial['title'] . ' tutorial.',
      'activeLink' => 'Tutorials',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'securityLevels' => $this->tutorialRepository->getAllLessonSecurityLevels(),
      'tutorial' => $tutorial,
      'singleSketch' => $tutorial['single_sketch'],
      'sketches' => $this->sketchRepository->getSketches($this->ownerOfStarterSketchesId),
      'submitButtonText' => 'Create'
    ));
    $this->addVar('errors');
    $this->addVar('formVars');
    $this->addVar('flash');
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

  private function redirectToHomePageWithWarning() {
    $this->flash('You are not authorised!', 'danger');
    $this->response->header('Location: /');
    return $this->response;
  }

  private function redirectToPageNotFound() {
    $this->response->header('Location: /page-not-found');
    return $this->response;
  }
}
