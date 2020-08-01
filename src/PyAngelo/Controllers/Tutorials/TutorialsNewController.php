<?php
namespace PyAngelo\Controllers\Tutorials;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;
use PyAngelo\Repositories\SketchRepository;

class TutorialsNewController extends Controller {
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
      return $this->redirectToLoginPageWithWarning();

    $this->response->setView('tutorials/new.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Create a New Tutorial',
      'metaDescription' => "Create a tutorial for PyAngelo which will consist of a number of video lessons.",
      'activeLink' => 'Tutorials',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'categories' => $this->tutorialRepository->getAllTutorialCategories(),
      'levels' => $this->tutorialRepository->getAllTutorialLevels(),
      'sketches' => $this->sketchRepository->getSketches($this->ownerOfStarterSketchesId),
      'submitButtonText' => 'Create'
    ));
    $this->addVar('errors');
    $this->addVar('formVars');
    $this->addVar('flash');
    return $this->response;
  }
 
  private function redirectToLoginPageWithWarning() {
    $this->flash('You are not authorised!', 'danger');
    $this->response->header('Location: /');
    return $this->response;
  }
}
