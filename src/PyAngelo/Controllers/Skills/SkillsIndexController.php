<?php
namespace PyAngelo\Controllers\Skills;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;

class SkillsIndexController extends Controller {
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
    $skills = $this->tutorialRepository->getAllSkills();
    $this->response->setView('skills/index.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'PyAngelo Skills Mastery',
      'metaDescription' => "Your mastery level for each of the skills taught on the PyAngelo website.",
      'activeLink' => 'Tutorials',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'skills' => $skills
    ));
    return $this->response;
  }
}
