<?php
namespace PyAngelo\Controllers\Blog;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\BlogRepository;
use Framework\{Request, Response};

class BlogCommentUnpublishController extends Controller {
  protected $blogRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    BlogRepository $blogRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->blogRepository = $blogRepository;
  }

  public function exec() {
    if (!$this->auth->isAdmin())
      return $this->redirectToHomeWithWarning('You are not authorised!');

    if (! $this->auth->crsfTokenIsValid())
      return $this->redirectToHomeWithWarning('You must delete comments from the PyAngelo website!');

    if (!isset($this->request->post['comment_id']))
      return $this->redirectToPageNotFound();

    $this->blogRepository->unpublishCommentById($this->request->post['comment_id']);

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
