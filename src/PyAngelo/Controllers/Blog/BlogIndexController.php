<?php
namespace PyAngelo\Controllers\Blog;

use Carbon\Carbon;
use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\BlogRepository;
use Framework\Contracts\PurifyContract;

class BlogIndexController extends Controller {
  protected $blogRepository;
  protected $purifier;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    BlogRepository $blogRepository,
    PurifyContract $purifier
  ) {
    parent::__construct($request, $response, $auth);
    $this->blogRepository = $blogRepository;
    $this->purifier = $purifier;
  }

  public function exec() {
    $this->request->session['redirect'] = $this->request->server['REQUEST_URI'];
    $this->response->setView('blog/index.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'PyAngelo Blog',
      'metaDescription' => "A list of interesting coding related blog posts from the creators of PyAngelo.",
      'activeLink' => 'Blog',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'blogs' => $this->blogRepository->getAllBlogs(),
      'featuredBlogs' => $this->blogRepository->getFeaturedBlogs(),
      'purifier' => $this->purifier
    ));
    return $this->response;
  }
}
