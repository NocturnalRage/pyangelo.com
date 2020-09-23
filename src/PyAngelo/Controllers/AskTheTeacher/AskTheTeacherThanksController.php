<?php
namespace PyAngelo\Controllers\AskTheTeacher;

use Framework\{Request, Response};
use Framework\Contracts\PurifyContract;
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\QuestionRepository;

class AskTheTeacherThanksController extends Controller {
  protected $questionRepository;
  protected $purifier;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    QuestionRepository $questionRepository,
    PurifyContract $purifier
  ) {
    parent::__construct($request, $response, $auth);
    $this->questionRepository = $questionRepository;
    $this->purifier = $purifier;
  }

  public function exec() {
    if (!isset($this->request->get['questionId'])) {
      $this->response->header('Location: /page-not-found');
      return $this->response;
    }
    if (! $question = $this->questionRepository->getQuestionById(
      $this->request->get['questionId']
    )) {
      $this->response->header('Location: /page-not-found');
      return $this->response;
    }
    $this->response->setView('ask-the-teacher/thanks.html.php');
    $this->response->setVars(array(
      'pageTitle' => $question['question_title'],
      'metaDescription' => strip_tags(substr($question['question'], 0, 200)),
      'activeLink' => 'Ask the Teacher',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'question' => $question,
      'purifier' => $this->purifier
    ));
    return $this->response;
  }
}
