<?php
namespace PyAngelo\Controllers\Lessons;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\FormServices\LessonFormService;

class LessonsUpdateController extends Controller {
  protected $lessonFormService;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    LessonFormService $lessonFormService
  ) {
    parent::__construct($request, $response, $auth);
    $this->lessonFormService = $lessonFormService;
  }

  public function exec() {
    if (!$this->auth->isAdmin()) {
      $this->flash('You are not authorised!', 'danger');
      $this->response->header('Location: /');
      return $this->response;
    }

    if (! $this->auth->crsfTokenIsValid()) {
      $this->flash('Please update the lesson from the PyAngelo website.', 'danger');
      $this->response->header('Location: /');
      return $this->response;
    }

    if (! isset($this->request->post['slug']) ||
        ! isset($this->request->post['lesson_slug'])

    ) {
      $this->response->header('Location: /page-not-found');
      return $this->response;
    }
    $success = $this->lessonFormService->updateLesson(
      $this->request->post,
      $this->request->files['poster']
    );
    if (!$success) {
      $this->request->session['errors'] = $this->lessonFormService->getErrors();
      $this->flash($this->lessonFormService->getFlashMessage(), 'danger');
      $this->request->session['formVars'] = $this->request->post;
      $location = 'Location: /tutorials/' . urlencode($this->request->post['slug']) . '/lessons/' . urlencode($this->request->post['lesson_slug']) . '/edit';
      $this->response->header($location);
      return $this->response;
    }

    $location = 'Location: /tutorials/' . urlencode($this->request->post['slug']) . '/' . urlencode($this->request->post['lesson_slug']);
    $this->response->header($location);
    return $this->response;
  }
}
