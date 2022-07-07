<?php
namespace PyAngelo\Controllers\Tutorials;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;
use PyAngelo\Repositories\SketchRepository;

class TutorialsEditController extends Controller {
  protected $tutorialRepository;
  protected $sketchRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    TutorialRepository $tutorialRepository,
    SketchRepository $sketchRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->tutorialRepository = $tutorialRepository;
    $this->sketchRepository = $sketchRepository;
    $this->ownerOfStarterSketchesId = 1;
  }

  public function exec() {
    if (!$this->auth->isAdmin())
      return $this->redirectToHomePageWithWarning();

    if (!isset($this->request->get['slug']))
      return $this->redirectToPageNotFound();

    if (!($tutorial = $this->tutorialRepository->getTutorialBySlug($this->request->get['slug'])))
      return $this->redirectToPageNotFound();

    $formVars = $_SESSION['formVars'] ?? $tutorial;
    unset($_SESSION['formVars']);

    $this->response->setView('tutorials/edit.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Edit ' . $tutorial['title'] . ' Tutorial',
      'metaDescription' => "Edit this PyAngelo tutorial.",
      'activeLink' => 'Tutorials',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'tutorial' => $tutorial,
      'categories' => $this->tutorialRepository->getAllTutorialCategories(),
      'levels' => $this->tutorialRepository->getAllTutorialLevels(),
      'sketches' => $this->sketchRepository->getSketches($this->ownerOfStarterSketchesId),
      'formVars' => $formVars,
      'submitButtonText' => 'Update'
    ));
    $this->addVar('errors');
    $this->addVar('flash');
    return $this->response;
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
