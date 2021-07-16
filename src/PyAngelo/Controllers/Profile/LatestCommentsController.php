<?php
namespace PyAngelo\Controllers\Profile;

use Carbon\Carbon;
use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;
use PyAngelo\Repositories\QuestionRepository;
use PyAngelo\Repositories\BlogRepository;

class LatestCommentsController extends Controller {
  protected $tutorialRepository;
  protected $questionRepository;
  protected $blogRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    TutorialRepository $tutorialRepository,
    QuestionRepository $questionRepository,
    BlogRepository $blogRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->tutorialRepository = $tutorialRepository;
    $this->questionRepository = $questionRepository;
    $this->blogRepository = $blogRepository;
  }

  public function exec() {
    $lessonPageNo = $this->getLessonPageNo();
    $questionPageNo = $this->getQuestionPageNo();
    $blogPageNo = $this->getBlogPageNo();
    $commentsPerPage = 10;

    $offset = ($lessonPageNo-1) * $commentsPerPage;
    $lessonComments = $this->tutorialRepository->getLatestComments($offset, $commentsPerPage);

    $offset = ($questionPageNo-1) * $commentsPerPage;
    $questionComments = $this->questionRepository->getLatestComments($offset, $commentsPerPage);

    $offset = ($blogPageNo-1) * $commentsPerPage;
    $blogComments = $this->blogRepository->getLatestComments($offset, $commentsPerPage);

    $this->response->setView('profile/latest-comments.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Latest Comments | PyAngelo',
      'metaDescription' => "The latest comments on the PyAngelo website.",
      'activeLink' => 'profile',
      'auth' => $this->auth,
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'commentsPerPage' => $commentsPerPage,
      'lessonComments' => $lessonComments,
      'lessonPageNo' => $lessonPageNo,
      'questionComments' => $questionComments,
      'questionPageNo' => $questionPageNo,
      'blogComments' => $blogComments,
      'blogPageNo' => $blogPageNo
    ));
    return $this->response;
  }

  private function getLessonPageNo() {
    if (!isset($this->request->get['lessonPageNo'])) {
      return 1;
    }
    else if (!ctype_digit($this->request->get['lessonPageNo'])) {
      return 1;
    }
    else {
      return $this->request->get['lessonPageNo'];
    }
  }

  private function getBlogPageNo() {
    if (!isset($this->request->get['blogPageNo'])) {
      return 1;
    }
    else if (!ctype_digit($this->request->get['blogPageNo'])) {
      return 1;
    }
    else {
      return $this->request->get['blogPageNo'];
    }
  }

  private function getQuestionPageNo() {
    if (!isset($this->request->get['questionPageNo'])) {
      return 1;
    }
    else if (!ctype_digit($this->request->get['questionPageNo'])) {
      return 1;
    }
    else {
      return $this->request->get['questionPageNo'];
    }
  }
}
