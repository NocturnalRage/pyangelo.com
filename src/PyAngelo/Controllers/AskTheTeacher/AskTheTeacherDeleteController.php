<?php
namespace PyAngelo\Controllers\AskTheTeacher;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\QuestionRepository;

class AskTheTeacherDeleteController extends Controller {
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
    if (! $this->auth->isAdmin()) {
      $this->flash('You are not authorised!', 'danger');
      $this->response->header('Location: /');
      return $this->response;
    }
    if (! $this->auth->crsfTokenIsValid()) {
      $this->flash('Please delete questions from the PyAngelo website!', 'danger');
      $this->response->header('Location: /ask-the-teacher/question-list');
      return $this->response;
    }

    if (empty($this->request->post['slug'])) {
      $this->flash('You must select a question to delete', 'danger');
      $this->response->header('Location: /ask-the-teacher/question-list');
      return $this->response;
    }

    $rowsDeleted = $this->questionRepository->deleteQuestion(
      $this->request->post['slug']
    );

    if ($rowsDeleted == 1) {
      $this->flash('The question has been deleted.', 'success');
    }
    else {
      $this->flash('Sorry, we could not delete the question.', 'danger');
    }

    $this->response->header('Location: /ask-the-teacher/question-list');
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
