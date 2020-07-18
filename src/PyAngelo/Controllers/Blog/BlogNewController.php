<?php
namespace PyAngelo\Controllers\Blog;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\BlogRepository;

class BlogNewController extends Controller {
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
      return $this->redirectToHomePageWithWarning();

    $this->response->setView('blog/new.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Create a New Blog',
      'metaDescription' => "Create an amazing new blog for the PyAngelo crowd.",
      'activeLink' => 'Blog',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'categories' => $this->blogRepository->getAllBlogCategories(),
      'submitButtonText' => 'Create'
    ));
    $this->addVar('errors');
    $this->addVar('formVars');
    $this->addVar('flash');
    return $this->response;
  }

  private function redirectToHomePageWithWarning() {
    $this->flash('You are not authorised!', 'danger');
    $this->response->header('Location: /');
    return $this->response;
  }
}
