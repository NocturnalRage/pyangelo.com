<?php
namespace PyAngelo\Controllers\AskTheTeacher;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\QuestionRepository;
use Framework\Contracts\PurifyContract;

class AskTheTeacherCreateController extends Controller {
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
    if (! $this->auth->loggedIn()) {
      $this->flash('You must be logged in to ask a question!', 'info');
      $this->response->header('Location: /login');
      return $this->response;
    }
    if (! $this->auth->crsfTokenIsValid()) {
      $this->flash('Please ask a question from the PyAngelo website!', 'danger');
      $this->response->header('Location: /');
      return $this->response;
    }

    if (empty($this->request->post['question_title'])) {
      $_SESSION['errors']['question_title'] = 'You must supply a title for this question.';
    }
    else if (strlen($this->request->post['question_title']) > 100) {
      $_SESSION['errors']['question_title'] = 'The title must be no more than 100 characters.';
    }
    if (empty($this->request->post['question'])) {
      $_SESSION['errors']['question'] = 'You must ask a question.';
    }

    if (! empty($_SESSION['errors'])) {
      $this->flash('There were some errors. Please fix these below and then submit your question again.', 'danger');
      $_SESSION['formVars'] = $this->request->post;
      $this->response->header('Location: /ask-the-teacher/ask');
      return $this->response;
    }
    
    $slug = $this->generateSlug($this->request->post['question_title']);

    $questionId = $this->questionRepository->createQuestion(
      $this->auth->personId(),
      $this->request->post['question_title'],
      $this->request->post['question'],
      $slug
    );

    $questionLink = $this->request->server['REQUEST_SCHEME'] . '://' .
                   $this->request->server['SERVER_NAME'] .
                   '/ask-the-teacher/' . $slug . '/edit';
    $question = $this->purifier->purify($this->request->post['question']);
    $questionTitle = $this->purifier->purify($this->request->post['question_title']);

    $this->response->header('Location: /ask-the-teacher/thanks-for-your-question?questionId=' . $questionId);
    return $this->response;
  }

  private function generateSlug($title) {
    $slug = substr($title, 0, 100);
    $slug = strtolower($slug);
    $slug = str_replace('.', '-', $slug);
    $slug = preg_replace('/[^a-z0-9 ]/', '', $slug);
    $slug = preg_replace('/\s+/', '-', $slug);
    $slug = trim($slug, '-');
    $slugVersion = 1;
    $unversionedSlug = $slug;
    while ($this->questionRepository->getQuestionBySlug($slug)) {
      $slugVersion++;
      $slug = $unversionedSlug . '-' . $slugVersion;
    }
    return $slug;
  }
}
