<?php
namespace PyAngelo\Controllers\Blog;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\BlogRepository;
use Framework\{Request, Response};

class BlogToggleAlertController extends Controller {
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
    $this->response->setView('blog/toggle-alert.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Log in to update your notifications'
      ));
      return $this->response;
    }

    // Is the CRSF Token Valid
    if (! $this->auth->crsfTokenIsValid()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'Please update your notifications from the PyAngelo website.'
      ));
      return $this->response;
    }

    if (empty($this->request->post['blogId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a blog to be notified about.'
      ));
      return $this->response;
    }

    if (! $blog = $this->blogRepository->getBlogById($this->request->post['blogId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a valid blog to be notified about.'
      ));
      return $this->response;
    }

    $alertUser = $this->blogRepository->shouldUserReceiveAlert(
      $this->request->post['blogId'],
      $this->auth->personId()
    );

    if (! $alertUser) {
      $this->blogRepository->addToBlogAlert(
        $this->request->post['blogId'],
        $this->auth->personId()
      );
      $this->response->setVars(array(
        'status' => 'success',
        'message' => 'Notifications are on for this blog'
      ));
    }
    else {
      $this->blogRepository->removeFromBlogAlert(
        $this->request->post['blogId'],
        $this->auth->personId()
      );
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Notifications are off for this blog'
      ));
    }
    return $this->response;
  }
}
