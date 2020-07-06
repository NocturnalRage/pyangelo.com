<?php
namespace PyAngelo\Controllers\Tutorials;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;

class TutorialsIndexController extends Controller {
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
    $tutorials = $this->tutorialRepository->getAllTutorials();
    $this->response->setView('tutorials/index.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'PyAngelo Tutorials',
      'metaDescription' => "Learn how to code using Python graphics programming in the browser.",
      'activeLink' => 'Tutorials',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'tutorials' => $tutorials
    ));
    return $this->response;
  }
}
