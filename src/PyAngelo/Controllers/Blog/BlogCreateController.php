<?php
namespace PyAngelo\Controllers\Blog;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\FormServices\BlogFormService;

class BlogCreateController extends Controller {
  protected $blogFormService;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    BlogFormService $blogFormService
  ) {
    parent::__construct($request, $response, $auth);
    $this->blogFormService = $blogFormService;
  }

  public function exec() {
    if (!$this->auth->isAdmin())
      return $this->redirectToHomePageWithWarnings();

    $success = $this->blogFormService->createBlog(
      $this->request->post,
      $this->request->files['blog_image']
    );
    if (!$success) {
      $this->request->session['errors'] = $this->blogFormService->getErrors();
      $this->flash($this->blogFormService->getFlashMessage(), 'danger');
      $this->request->session['formVars'] = $this->request->post;
      $this->response->header('Location: /blog/new');
      return $this->response;
    }
    $this->response->header('Location: /blog');
    return $this->response;
  }

  private function redirectToHomePageWithWarnings() {
    $this->flash('You are not authorised!', 'danger');
    $this->response->header('Location: /');
    return $this->response;
  }
}
