<?php
namespace PyAngelo\Controllers\AskTheTeacher;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\QuestionRepository;
use Framework\{Request, Response};
use Framework\Contracts\PurifyContract;
use Framework\Contracts\AvatarContract;

class AskTheTeacherCommentController extends Controller {
  protected $questionRepository;
  protected $purifier;
  protected $avatar;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    QuestionRepository $questionRepository,
    PurifyContract $purifier,
    AvatarContract $avatar
  ) {
    parent::__construct($request, $response, $auth);
    $this->questionRepository = $questionRepository;
    $this->purifier = $purifier;
    $this->avatar = $avatar;
  }

  public function exec() {
    $this->response->setView('ask-the-teacher/question-comment.json.php');
    $this->response->header('Content-Type: application/json');

    $errorsCommentHtml = 'We could not add your comment due to errors.';

    // Are we logged in?
    if (!$this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('You must be logged in to add a comment.'),
        'commentHtml' => json_encode($errorsCommentHtml)
      ));
      return $this->response;
    }

    // Is the CRSF Token Valid
    if (! $this->auth->crsfTokenIsValid()) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('Please add a comment from the PyAngelo website.'),
        'commentHtml' => json_encode($errorsCommentHtml)
      ));
      return $this->response;
    }

    if (empty($this->request->post['questionId'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('Please add a comment to a question.'),
        'commentHtml' => json_encode($errorsCommentHtml)
      ));
      return $this->response;
    }

    if (empty($this->request->post['questionComment'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('A comment must contain some text.'),
        'commentHtml' => json_encode($errorsCommentHtml)
      ));
      return $this->response;
    }

    // Is the question id valid
    if (!($question = $this->questionRepository->getQuestionById($this->request->post['questionId']))) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('You must add a comment to a valid question.'),
        'commentHtml' => json_encode($errorsCommentHtml)
      ));
      return $this->response;
    }

    $commentData = [
      'question_id' => $question['question_id'],
      'person_id' => $this->auth->personId(),
      'question_comment' => $this->request->post['questionComment'],
      'published' => 1
    ];
    $commentId = $this->questionRepository->insertQuestionComment($commentData);
    $this->questionRepository->updateQuestionLastUpdatedDate(
      $question['question_id']
    );

    // Output the comment HTML to the view
    $displayName = htmlspecialchars($this->auth->person()['given_name'] . ' ' . $this->auth->person()['family_name'], ENT_QUOTES, 'UTF-8');
    $cleanCommentHtml = $this->purifier->purify($this->request->post['questionComment']);
    $avatarUrl = $this->avatar->getAvatarUrl($this->auth->person()['email']);
    $commentHtml = <<<ENDHTML
    <div class="media">
      <div class="media-left">
        <a href="#">
          <img class="media-object" src="$avatarUrl" alt="$displayName" />
        </a>
      </div>
      <div class="media-body">
        <h4 class="media-heading">$displayName <small><i>Posted now</i></small></h4>
        <p>$cleanCommentHtml</p>
      </div>
      <hr />
    </div>
ENDHTML;

    $this->notifyFollowers($question, $commentId);

    $this->response->setVars(array(
      'status' => json_encode('success'),
      'message' => json_encode('Your comment has been added.'),
      'commentHtml' => json_encode($commentHtml)
    ));
    return $this->response;
  }

  private function notifyFollowers($question, $commentId) {
    $person = $this->auth->person();
    $followers = $this->questionRepository->getFollowers($question['question_id']);
    $commentLink = '/ask-the-teacher/' . $question['slug'] . '#comment_' . $commentId;
    $this->avatar->setSizeInPixels(25);
    $avatarUrl = $this->avatar->getAvatarUrl($person['email']);
    foreach ($followers as $follower) {
      if ($follower['person_id'] != $person['person_id']) {
        $data_json = json_encode([
          'message' => $person['given_name'] . ' ' . $person['family_name'] . ' left a comment on the question "' . $question['question_title'] . '"',
          'link' => $commentLink,
          'avatarUrl' => $avatarUrl,
          'isAdmin' => $this->auth->isAdmin()
        ]);
        $this->auth->createNotification(
          $follower['person_id'],
          $question['question_id'],
          'question',
          $data_json
        );
      }
    }
  }
}
