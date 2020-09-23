<?php
namespace PyAngelo\Controllers\AskTheTeacher;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\QuestionRepository;
use Framework\{Request, Response};

class AskTheTeacherCommentUnpublishController extends Controller {
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

    // Is the CRSF Token Valid
    if (! $this->auth->crsfTokenIsValid()) {
      $this->flash('You must delete comments from the PyAngelo website!', 'danger');
      $this->response->header('Location: /');
      return $this->response;
    }

    if (!isset($this->request->post['comment_id'])) {
      $this->response->header('Location: /page-not-found');
      return $this->response;
    }

    $this->questionRepository->unpublishCommentById($this->request->post['comment_id']);

    $location = $this->request->server['HTTP_REFERER'] ?? '/';
    $this->response->header("Location: $location");
    return $this->response;
  }
}
