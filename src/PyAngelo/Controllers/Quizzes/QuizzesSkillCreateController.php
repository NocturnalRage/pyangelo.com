<?php
namespace PyAngelo\Controllers\Quizzes;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;
use PyAngelo\Repositories\QuizRepository;

class QuizzesSkillCreateController extends Controller {
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

    if (! $this->auth->crsfTokenIsValid())
      return $this->redirectToHomePageDueToInvalidCrsfToken();

    if (!isset($this->request->post['slug']))
      return $this->redirectToPageNotFound();

    if (!isset($this->request->post['skill_slug']))
      return $this->redirectToPageNotFound();

    if (!($tutorial = $this->tutorialRepository->getTutorialBySlug(
      $this->request->post['slug']
    )))
      return $this->redirectToPageNotFound();

    if (!($skill = $this->quizRepository->getSkillBySlug(
      $this->request->post['skill_slug']
    )))
      return $this->redirectToPageNotFound();

    $quizId = $this->getOrCreateQuiz($skill['skill_id'], $this->auth->personId());

    $this->response->header('Location: /tutorials/' . $this->request->post['slug'] . '/' . $this->request->post['skill_slug'] . '/quizzes');
    return $this->response;
  }

  private function getOrCreateQuiz($skillId, $personId) {
    if ($quiz = $this->quizRepository->getIncompleteSkillQuiz($skillId, $personId)) {
      return $quiz['quiz_id'];
    }
    $questionBank = $this->quizRepository->getAllSkillQuestions(
      $skillId
    );
    shuffle($questionBank);
    $totalQuestions = 7;
    $quizTypeId = 1; /* Skill Quiz */
    $quizId = $this->quizRepository->createQuiz($quizTypeId, $skillId, $personId);
    if ($totalQuestions > count($questionBank)) {
      $totalQuestions = count($questionBank);
    }
    for ($i = 0; $i < $totalQuestions; $i++) {
      $this->quizRepository->addQuizQuestion(
        $quizId,
        $questionBank[$i]["skill_question_id"]
      );
    }
    return $quizId;
  }

  private function redirectToHomePageDueToInvalidCrsfToken() {
    $this->flash('Please attempt the quiz from the PyAngelo website.', 'danger');
    $this->response->header('Location: /');
    return $this->response;
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
