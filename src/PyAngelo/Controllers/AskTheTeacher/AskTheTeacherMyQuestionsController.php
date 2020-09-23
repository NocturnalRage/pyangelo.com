<?php
namespace PyAngelo\Controllers\AskTheTeacher;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\QuestionRepository;

class AskTheTeacherMyQuestionsController extends Controller {
  protected $questionRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    QuestionRepository $questionRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->questionRepository = $questionRepository;
  }

  public function exec() {
    if (!$this->auth->loggedIn()) {
      $this->request->session['redirect'] = $this->request->server['REQUEST_URI'];
      $this->flash('You must be logged in to view your questions!', 'danger');
      $this->response->header('Location: /login');
      return $this->response;
    }

    $allQuestions = $this->getMyQuestions();
    $unansweredQuestions = array_filter($allQuestions, function($question) {
      return ! $question['answered_at'];
    });
    $questions = array_filter($allQuestions, function($question) {
      return $question['answered_at'];
    });

    $this->response->setView('ask-the-teacher/my-questions.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'My Questions',
      'metaDescription' => "A list of all the questions I have asked.",
      'activeLink' => 'Ask the Teacher',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'unansweredQuestions' => $unansweredQuestions,
      'questions' => $questions
    ));
    $this->addVar('flash');
    return $this->response;
  }

  public function getMyQuestions() {
    return $this->questionRepository->getQuestionsByPersonId($this->auth->personId());
  }
}
