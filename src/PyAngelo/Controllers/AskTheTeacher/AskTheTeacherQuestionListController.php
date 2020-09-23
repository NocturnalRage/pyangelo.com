<?php
namespace PyAngelo\Controllers\AskTheTeacher;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\QuestionRepository;

class AskTheTeacherQuestionListController extends Controller {
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
    if (!$this->auth->isAdmin()) {
      $this->flash('You are not authorised!', 'danger');
      $this->response->header('Location: /');
      return $this->response;
    }

    $questions = $this->questionRepository->getUnansweredQuestions();

    $this->response->setView('ask-the-teacher/question-list.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'The List of Unanswered Questions',
      'metaDescription' => "This page let's you have heaps of fun answering the wonderful coding questions from users all around the World.",
      'activeLink' => 'Ask the Teacher',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'questions' => $questions
    ));
    $this->addVar('flash');
    return $this->response;
  }
}
