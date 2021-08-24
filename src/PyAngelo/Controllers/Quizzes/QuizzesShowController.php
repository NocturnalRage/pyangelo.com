<?php
namespace PyAngelo\Controllers\Quizzes;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;

class QuizzesShowController extends Controller {
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
    if (! $this->auth->loggedIn())
      return $this->redirectToLoginPage();

    if (!isset($this->request->get['slug']))
      return $this->redirectToPageNotFound();

    if (! $tutorial = $this->tutorialRepository->getTutorialBySlug(
      $this->request->get['slug']
    ))
      return $this->redirectToPageNotFound();

    if ($tutorialQuizInfo = $this->tutorialRepository->getUncompletedTutorialQuizInfo(
      $tutorial['tutorial_id'],
      $this->auth->personId()
    )) {
       $this->response->setView('quizzes/show.html.php');
       $this->response->setVars(array(
         'pageTitle' => $tutorial['title'] . ' Quiz',
         'metaDescription' => 'Take a quiz to show what you have learnt on the PyAngelo website',
         'activeLink' => 'Tutorials',
         'tutorial' => $tutorial,
         'tutorialQuizInfo' => $tutorialQuizInfo,
         'personInfo' => $this->auth->getPersonDetailsForViews()
       ));
       return $this->response;
    }
    else {
        $skills = $this->tutorialRepository->getTutorialSkills(
          $tutorial['tutorial_id'],
          $this->auth->personId()
        );
       $this->response->setView('quizzes/show-quiz-not-created.html.php');
       $this->response->setVars(array(
         'pageTitle' => $tutorial['title'] . ' Quiz',
         'metaDescription' => 'Take a quiz to show what you have learnt on the PyAngelo website',
         'activeLink' => 'Tutorials',
         'tutorial' => $tutorial,
         'skills' => $skills,
         'personInfo' => $this->auth->getPersonDetailsForViews()
       ));
       return $this->response;
    }
  }

  private function redirectToLoginPage() {
    $this->response->header('Location: /login');
    return $this->response;
  }

  private function redirectToPageNotFound() {
    $this->response->header('Location: /page-not-found');
    return $this->response;
  }

}
