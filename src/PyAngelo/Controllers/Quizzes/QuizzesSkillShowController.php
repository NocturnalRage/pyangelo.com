<?php
namespace PyAngelo\Controllers\Quizzes;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;
use PyAngelo\Repositories\QuizRepository;

class QuizzesSkillShowController extends Controller {
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
    if (! $this->auth->loggedIn())
      return $this->redirectToLoginPage();

    if (!isset($this->request->get['slug']))
      return $this->redirectToPageNotFound();

    if (!isset($this->request->get['skill_slug']))
      return $this->redirectToPageNotFound();

    if (! $tutorial = $this->tutorialRepository->getTutorialBySlug(
      $this->request->get['slug']
    ))
      return $this->redirectToPageNotFound();

    if (! $skill = $this->quizRepository->getSkillBySlug(
      $this->request->get['skill_slug']
    ))
      return $this->redirectToPageNotFound();

    if ($quizInfo = $this->quizRepository->getIncompleteSkillQuizInfo(
      $skill['skill_id'],
      $this->auth->personId()
    )) {
       $this->response->setView('quizzes/show.html.php');
       $this->response->setVars(array(
         'pageTitle' => $skill['skill_name'] . ' Quiz',
         'metaDescription' => 'Take a skill quiz to show what you have learnt on the PyAngelo website',
         'activeLink' => 'Tutorials',
         'tutorial' => $tutorial,
         'skill' => $skill,
         'quizInfo' => $quizInfo,
         'personInfo' => $this->auth->getPersonDetailsForViews()
       ));
       return $this->response;
    }
    else {
        $skills = $this->quizRepository->getSkillMastery(
          $skill['skill_id'],
          $this->auth->personId()
        );
       $this->response->setView('quizzes/show-quiz-not-created.html.php');
       $this->response->setVars(array(
         'pageTitle' => $skill['skill_name'] . ' Quiz',
         'metaDescription' => 'Take a skill quiz to show what you have learnt on the PyAngelo website',
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
