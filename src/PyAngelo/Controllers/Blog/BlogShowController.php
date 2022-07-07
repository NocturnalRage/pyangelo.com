<?php
namespace PyAngelo\Controllers\Blog;

use Carbon\Carbon;
use Framework\{Request, Response};
use Framework\Contracts\PurifyContract;
use Framework\Contracts\AvatarContract;
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\BlogRepository;

class BlogShowController extends Controller {
  protected $blogRepository;
  protected $purifier;
  protected $avatar;
  protected $showCommentCount;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    BlogRepository $blogRepository,
    PurifyContract $purifier,
    AvatarContract $avatar,
    $showCommentCount
  ) {
    parent::__construct($request, $response, $auth);
    $this->blogRepository = $blogRepository;
    $this->purifier = $purifier;
    $this->avatar = $avatar;
    $this->showCommentCount = $showCommentCount;
  }

  public function exec() {
    if (!isset($this->request->get['slug']))
      return $this->redirectToPageNotFound();

    if (! $blog = $this->blogRepository->getBlogBySlug($this->request->get['slug']))
      return $this->redirectToPageNotFound();

    $alertUser = false;
    if ($this->auth->loggedIn()) {
      $alertUser = $this->blogRepository->shouldUserReceiveAlert(
        $blog['blog_id'],
        $this->auth->personId()
      ) ? true : false;
    }
    else {
      $_SESSION['redirect'] = $this->request->server['REQUEST_URI'];
    }

    $comments = $this->blogRepository->getPublishedBlogComments($blog['blog_id']);
    foreach ($comments as &$comment) {
      $comment['created_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $comment['created_at'])->diffForHumans();
    }

    $this->response->setView('blog/show.html.php');
    $this->response->setVars(array(
      'pageTitle' => $blog['title'],
      'metaDescription' => strip_tags($blog['preview']),
      'activeLink' => 'Blog',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'blog' => $blog,
      'alertUser' => $alertUser,
      'comments' => $comments,
      'purifier' => $this->purifier,
      'avatar' => $this->avatar,
      'showCommentCount' => $this->showCommentCount
    ));
    return $this->response;
  }

  private function redirectToPageNotFound() {
    $this->response->header('Location: /page-not-found');
    return $this->response;
  }
}
