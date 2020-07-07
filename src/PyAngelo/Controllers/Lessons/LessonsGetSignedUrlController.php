<?php
namespace PyAngelo\Controllers\Lessons;

use Framework\{Request, Response};
use Framework\CloudFront\CloudFront;
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;

class LessonsGetSignedUrlController extends Controller {
  protected $tutorialRepository;
  protected $cloudFront;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    TutorialRepository $tutorialRepository,
    CloudFront $cloudFront
  ) {
    parent::__construct($request, $response, $auth);
    $this->tutorialRepository = $tutorialRepository;
    $this->cloudFront = $cloudFront;
  }

  public function exec() {
    $this->response->setView('lessons/signed-url.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $lesson = $this->getLessonById()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'No such lesson.',
        'signedUrl' => ''
      ));
      return $this->response;
    }

    if ($this->premiumLessonAndNotAuthorised($lesson)) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must be a premium member to view this lesson.',
        'signedUrl' => ''
      ));
      return $this->response;
    } 
    if ($this->freeLessonAndNotLoggedIn($lesson)) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must create a free account to view this lesson.',
        'signedUrl' => ''
      ));
      return $this->response;
    }

    $lesson['youtube_url'] = ($this->auth->person()['country_code'] ?? 'ZZ') == 'CN' ? '' : $lesson['youtube_url'];

    if (! $lesson['youtube_url']) {
      $expires = time()+(60*60);
      $signedUrl = $this->cloudFront->generateSignedUrl (
        $lesson['video_name'],
        $expires
      );
    }
    else {
      $signedUrl = '';
    }

    $this->response->setVars(array(
      'status' => 'success',
      'message' => 'The signed url was successfully generated.',
      'signedUrl' => $signedUrl,
      'youtubeUrl' => $lesson['youtube_url']
    ));
    return $this->response;
  }

  private function getLessonById() {
    if (! isset($this->request->post['lessonId'])) {
      return false;
    }

    return $this->tutorialRepository->getLessonById(
      $this->request->post['lessonId']
    );
  }

  private function premiumLessonAndNotAuthorised($lesson) {
    return ($this->lessonIsPremium($lesson) && ! $this->auth->isPremium());
  }

  private function lessonIsPremium($lesson) {
    return $lesson['lesson_security_level_id'] == 3;
  }

  private function freeLessonAndNotLoggedIn($lesson) {
    return ($this->lessonIsFree($lesson) && ! $this->auth->loggedIn());
  }

  private function lessonIsFree($lesson) {
    return $lesson['lesson_security_level_id'] == 2;
  }
}
