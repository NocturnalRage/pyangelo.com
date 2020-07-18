<?php
namespace PyAngelo\Controllers\Blog;

use PyAngelo\Auth\Auth;
use Framework\{Request, Response};
use PyAngelo\Controllers\Controller;
use PyAngelo\FormServices\BlogFormService;

class BlogUpdateController extends Controller {
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
      return $this->redirectToHomeWithWarning();

    if (!isset($this->request->post['slug']))
      return $this->redirectToPageNotFound();

    $success = $this->blogFormService->updateBlog(
      $this->request->post,
      $this->request->files['blog_image']
    );
    if (!$success) {
      $this->request->session['errors'] = $this->blogFormService->getErrors();
      $this->flash($this->blogFormService->getFlashMessage(), 'danger');
      $this->request->session['formVars'] = $this->request->post;
      $location = 'Location: /blog/' . urlencode($this->request->post['slug']) . '/edit';
      $this->response->header($location);
      return $this->response;
    }

    $location = 'Location: /blog/' . urlencode($this->request->post['slug']);
    $this->response->header($location);
    return $this->response;
  }

  private function redirectToHomeWithWarning() {
    $this->flash('You are not authorised!', 'danger');
    $this->response->header('Location: /');
    return $this->response;
  }

  private function redirectToPageNotFound() {
    $this->response->header('Location: /page-not-found');
    return $this->response;
  }
}
