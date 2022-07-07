<?php
namespace PyAngelo\Controllers\Lessons;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\FormServices\LessonFormService;

class LessonsCreateController extends Controller {
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
    if (!$this->auth->isAdmin())
      return $this->redirectToHomePageWithWarning();

    if (! $this->auth->crsfTokenIsValid())
      return $this->redirectToHomePageDueToInvalidCrsfToken();

    $success = $this->lessonFormService->createLesson(
      $this->request->post,
      $this->request->files['poster']
    );
    if (!$success)
      return $this->redirectBackToForm();

    $this->response->header('Location: /tutorials/' . $this->request->post['slug']);
    return $this->response;
  }

  private function redirectToHomePageWithWarning() {
    $this->flash('You are not authorised!', 'danger');
    $this->response->header('Location: /');
    return $this->response;
  }

  private function redirectToHomePageDueToInvalidCrsfToken() {
    $this->flash('Please create the lesson from the PyAngelo website.', 'danger');
    $this->response->header('Location: /');
    return $this->response;
  }

  private function redirectBackToForm() {
    $_SESSION['errors'] = $this->lessonFormService->getErrors();
    $this->flash($this->lessonFormService->getFlashMessage(), 'danger');
    $_SESSION['formVars'] = $this->request->post;
    $location = 'Location: /tutorials/' .
      ($this->request->post['slug'] ?? 'no-lesson') .
      '/lessons/new';
    $this->response->header($location);
    return $this->response;
  }
}
