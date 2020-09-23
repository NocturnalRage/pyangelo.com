<?php
namespace PyAngelo\Controllers\AskTheTeacher;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\QuestionRepository;

class AskTheTeacherEditController extends Controller {
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

    if (! $question = $this->getQuestionBySlug()) {
      $this->response->header('Location: /page-not-found');
      return $this->response;
    }

    $formVars = $this->request->session['formVars'] ?? $question;
    unset($this->request->session['formVars']);

    $this->response->setView('ask-the-teacher/edit.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Answer Question' ,
      'metaDescription' => 'Answer the ' . $question['question_title'] . ' question.',
      'activeLink' => 'Ask the Teacher',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'questionTypes' => $this->questionRepository->getAllQuestionTypes(),
      'question' => $question,
      'formVars' => $formVars
    ));
    $this->addVar('errors');
    $this->addVar('flash');
    return $this->response;
  }

  private function getQuestionBySlug() {
    if (! isset($this->request->get['slug'])) {
      return false;
    }

    return $this->questionRepository->getQuestionBySlug(
      $this->request->get['slug']
    );
  }
}
