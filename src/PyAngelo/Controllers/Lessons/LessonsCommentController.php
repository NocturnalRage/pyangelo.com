<?php
namespace PyAngelo\Controllers\Lessons;

use PyAngelo\Auth\Auth;
use PyAngelo\Email\NewCommentEmail;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;
use Framework\{Request, Response};
use Framework\Contracts\PurifyContract;
use Framework\Contracts\AvatarContract;

class LessonsCommentController extends Controller {
  protected $tutorialRepository;
  protected $purifier;
  protected $avatar;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    TutorialRepository $tutorialRepository,
    PurifyContract $purifier,
    AvatarContract $avatar
  ) {
    parent::__construct($request, $response, $auth);
    $this->tutorialRepository = $tutorialRepository;
    $this->purifier = $purifier;
    $this->avatar = $avatar;
  }

  public function exec() {
    $this->response->setView('lessons/lesson-comment.json.php');
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

    if (empty($this->request->post['lessonId'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('Please add a comment to a lesson.'),
        'commentHtml' => json_encode($errorsCommentHtml)
      ));
      return $this->response;
    }

    if (empty($this->request->post['lessonComment'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('A comment must contain some text.'),
        'commentHtml' => json_encode($errorsCommentHtml)
      ));
      return $this->response;
    }

    // Is the lesson id valid
    if (!($lesson = $this->tutorialRepository->getLessonById($this->request->post['lessonId']))) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('You must add a comment to a valid lesson.'),
        'commentHtml' => json_encode($errorsCommentHtml)
      ));
      return $this->response;
    }

    $cleanCommentHtml = $this->purifier->purify($this->request->post['lessonComment']);
    $commentData = [
      'lesson_id' => $lesson['lesson_id'],
      'person_id' => $this->auth->personId(),
      'lesson_comment' => $cleanCommentHtml,
      'published' => 1
    ];
    $commentId = $this->tutorialRepository->insertLessonComment($commentData);

    // Output the comment HTML to the view
    $displayName = htmlspecialchars($this->auth->person()['given_name'] . ' ' . $this->auth->person()['family_name'], ENT_QUOTES, 'UTF-8');
    $avatarUrl = $this->avatar->getAvatarUrl($this->auth->person()['email']);
    $commentHtml = <<<ENDHTML
    <div class="media">
      <div class="media-left">
        <img class="media-object" src="$avatarUrl" alt="$displayName" />
      </div>
      <div class="media-body">
        <h4 class="media-heading">$displayName <small><i>Posted now</i></small></h4>
        <p>$cleanCommentHtml</p>
      </div>
      <hr />
    </div>
ENDHTML;

    $this->notifyFollowers($lesson, $commentId);

    $this->response->setVars(array(
      'status' => json_encode('success'),
      'message' => json_encode('Your comment has been added.'),
      'commentHtml' => json_encode($commentHtml)
    ));
    return $this->response;
  }

  private function notifyFollowers($lesson, $commentId) {
    $person = $this->auth->person();
    $followers = $this->tutorialRepository->getFollowers($lesson['lesson_id']);
    $commentLink = '/tutorials/' . $lesson['tutorial_slug'] .
                   '/' . $lesson['lesson_slug'] .
                   '#comment_' . $commentId;
    $this->avatar->setSizeInPixels(25);
    $avatarUrl = $this->avatar->getAvatarUrl($person['email']);
    foreach ($followers as $follower) {
      if ($follower['person_id'] != $person['person_id']) {
        $data_json = json_encode([
          'message' => $person['given_name'] . ' ' . $person['family_name'] . ' left a comment on the lesson "' . $lesson['lesson_title'] . '"',
          'link' => $commentLink,
          'avatarUrl' => $avatarUrl,
          'isAdmin' => $this->auth->isAdmin()
        ]);
        $this->auth->createNotification(
          $follower['person_id'],
          $lesson['lesson_id'],
          'lesson',
          $data_json
        );
      }
    }
  }
}
