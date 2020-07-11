<?php
namespace PyAngelo\Controllers\Profile;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\BlogRepository;
use PyAngelo\Repositories\TutorialRepository;

class UnsubscribeThreadController extends Controller {
  protected $blogRepository;
  protected $tutorialRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    BlogRepository $blogRepository,
    TutorialRepository $tutorialRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->blogRepository = $blogRepository;
    $this->tutorialRepository = $tutorialRepository;
  }

  public function exec() {
    $this->response->setView('profile/unsubscribe-thread.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must be logged in to unsubscribe from a thread.'
      ));
      return $this->response;
    }

    if (! $this->auth->crsfTokenIsValid()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must perform this action from the PyAngelo website.'
      ));
      return $this->response;
    }

    if (empty($this->request->post['notificationTypeId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a thread to unsubscribe from.'
      ));
      return $this->response;
    }
    if (empty($this->request->post['notificationType'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a thread type to unsubscribe from.'
      ));
      return $this->response;
    }

    switch($this->request->post['notificationType']) {
      case 'blog':
        $this->blogRepository->removeFromBlogAlert(
          $this->request->post['notificationTypeId'],
          $this->auth->personId()
        );
        $status = 'success';
        $message = 'You will not receive any more notifications about this blog.';
        break;
      case 'lesson':
        $this->tutorialRepository->removeFromLessonAlert(
          $this->request->post['notificationTypeId'],
          $this->auth->personId()
        );
        $status = 'success';
        $message = 'You will not receive any more notifications about this lesson.';
        break;
      default:
        $status = 'error';
        $message = 'We did not know how to unsubscribe you from this thread.';
    } 

    $this->response->setVars(array(
      'status' => $status,
      'message' => $message
    ));
    return $this->response;
  }
}
