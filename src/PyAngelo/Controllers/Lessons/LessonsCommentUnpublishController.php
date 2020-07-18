<?php
namespace PyAngelo\Controllers\Lessons;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;
use Framework\{Request, Response};

class LessonsCommentUnpublishController extends Controller {
  protected $tutorialRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    TutorialRepository $tutorialRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->tutorialRepository = $tutorialRepository;
  }

  public function exec() {
    if (!$this->auth->isAdmin())
      return $this->redirectToHomeWithWarning('You are not authorised!');

    // Is the CRSF Token Valid
    if (! $this->auth->crsfTokenIsValid())
      return $this->redirectToHomeWithWarning('You must delete comments from the PyAngelo website!');

    if (!isset($this->request->post['comment_id']))
      return $this->redirectToPageNotFound();

    $this->tutorialRepository->unpublishCommentById($this->request->post['comment_id']);

    $location = $this->request->server['HTTP_REFERER'] ?? '/';
    $this->response->header("Location: $location");
    return $this->response;
  }

  private function redirectToHomeWithWarning($warning) {
    $this->flash($warning, 'danger');
    $this->response->header('Location: /');
    return $this->response;
  }

  private function redirectToPageNotFound() {
    $this->response->header('Location: /page-not-found');
    return $this->response;
  }
}
