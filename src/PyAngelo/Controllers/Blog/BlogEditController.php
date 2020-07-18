<?php
namespace PyAngelo\Controllers\Blog;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\BlogRepository;

class BlogEditController extends Controller {
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
      return $this->redirectToHomeWithWarning();

    if (!isset($this->request->get['slug']))
      return $this->redirectToPageNotFound();

    if (!($blog = $this->blogRepository->getBlogBySlug($this->request->get['slug'])))
      return $this->redirectToPageNotFound();

    $formVars = $this->request->session['formVars'] ?? $blog;
    unset($this->request->session['formVars']);

    $this->response->setView('blog/edit.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Edit ' . $blog['title'] . ' Blog',
      'metaDescription' => "Edit this PyAngelo blog.",
      'activeLink' => 'Blog',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'blog' => $blog,
      'categories' => $this->blogRepository->getAllBlogCategories(),
      'formVars' => $formVars,
      'submitButtonText' => 'Update'
    ));
    $this->addVar('errors');
    $this->addVar('flash');
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
