<?php
namespace PyAngelo\Controllers\AskTheTeacher;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\QuestionRepository;

class AskTheTeacherIndexController extends Controller {
  protected $questionRepository;
  protected $questionsPerPage;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    QuestionRepository $questionRepository,
    $questionsPerPage
  ) {
    parent::__construct($request, $response, $auth);
    $this->questionRepository = $questionRepository;
    $this->questionsPerPage = $questionsPerPage;
  }

  public function exec() {
    $this->request->session['redirect'] = $this->request->server['REQUEST_URI'];
    $pageNo = $this->getPageNo();
    $offset = ($pageNo-1) * $this->questionsPerPage;

    $questions = $this->questionRepository->getLatestQuestions(
      $offset, $this->questionsPerPage
    );
    $this->response->setView('ask-the-teacher/index.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Coding Questions Answered by Teachers',
      'metaDescription' => "We've answered thousands of coding questions in class and now we are answering them on the PyAngelo website. So if you've got a question, we've probably answered it! And if you can't find what you're looking for, you can always ask the teacher your own question.",
      'activeLink' => 'Ask the Teacher',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'pageNo' => $pageNo,
      'questionsPerPage' => $this->questionsPerPage,
      'questions' => $questions
    ));
    return $this->response;
  }

  private function getPageNo() {
    if (!isset($this->request->get['pageNo'])) {
      return 1;
    }
    else if (!is_int((int)$this->request->get['pageNo'])) {
      return 1;
    }
    else {
      return $this->request->get['pageNo'];
    }
  }
}
