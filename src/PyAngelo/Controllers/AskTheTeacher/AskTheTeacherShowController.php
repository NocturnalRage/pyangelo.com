<?php
namespace PyAngelo\Controllers\AskTheTeacher;

use Carbon\Carbon;
use Framework\{Request, Response};
use Framework\Contracts\PurifyContract;
use Framework\Contracts\AvatarContract;
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\QuestionRepository;

class AskTheTeacherShowController extends Controller {
  protected $questionRepository;
  protected $purifier;
  protected $avatar;
  protected $showCommentCount;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    QuestionRepository $questionRepository,
    PurifyContract $purifier,
    AvatarContract $avatar,
    $showCommentCount
  ) {
    parent::__construct($request, $response, $auth);
    $this->questionRepository = $questionRepository;
    $this->purifier = $purifier;
    $this->avatar = $avatar;
    $this->showCommentCount = $showCommentCount;
  }

  public function exec() {
    if (!isset($this->request->get['slug'])) {
      $this->response->header('Location: /page-not-found');
      return $this->response;
    }
    if (! $question = $this->questionRepository->getQuestionBySlugWithStatus(
      $this->request->get['slug'],
      $this->auth->personId()
    )) {
      $this->response->header('Location: /page-not-found');
      return $this->response;
    }
    $nextQuestion = $this->questionRepository->getNextQuestion(
      $question['updated_at']
    );
    $previousQuestion = $this->questionRepository->getPreviousQuestion(
      $question['updated_at']
    );

    $alertUser = false;
    if ($this->auth->loggedIn()) {
      $alertUser = $this->questionRepository->shouldUserReceiveAlert(
        $question['question_id'],
        $this->auth->personId()
      ) ? true : false;
    }
    else {
      $this->request->session['redirect'] = $this->request->server['REQUEST_URI'];
    }

    $comments = $this->questionRepository->getPublishedQuestionComments($question['question_id']);
    foreach ($comments as &$comment) {
      $comment['created_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $comment['created_at'])->diffForHumans();
    }

    $this->response->setView('ask-the-teacher/show.html.php');
    $this->response->setVars(array(
      'pageTitle' => $question['question_title'],
      'metaDescription' => strip_tags(substr($question['question'], 0, 200)),
      'activeLink' => 'Ask the Teacher',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'question' => $question,
      'alertUser' => $alertUser,
      'nextQuestion' => $nextQuestion,
      'previousQuestion' => $previousQuestion,
      'comments' => $comments,
      'purifier' => $this->purifier,
      'avatar' => $this->avatar,
      'showCommentCount' => $this->showCommentCount
    ));
    return $this->response;
  }
}
