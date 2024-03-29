<?php
namespace PyAngelo\Controllers\Quizzes;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;
use PyAngelo\Repositories\QuizRepository;

class QuizzesCreateController extends Controller {
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

    if (!($tutorial = $this->tutorialRepository->getTutorialBySlug(
      $this->request->post['slug']
    )))
      return $this->redirectToPageNotFound();

    $quizId = $this->getOrCreateQuiz($tutorial['tutorial_id'], $this->auth->personId());

    $this->response->header('Location: /tutorials/' . $this->request->post['slug'] . '/quizzes');
    return $this->response;
  }

  private function getOrCreateQuiz($tutorialId, $personId) {
    if ($quiz = $this->quizRepository->getIncompleteTutorialQuiz($tutorialId, $personId)) {
      return $quiz['quiz_id'];
    }
    $questionBank = $this->quizRepository->getAllTutorialQuestions(
      $tutorialId
    );
    shuffle($questionBank);
    $totalQuestions = 20;
    $quizTypeId = 2; /* Tutorial Quiz */
    $quizId = $this->quizRepository->createQuiz($quizTypeId, $tutorialId, $personId);
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
