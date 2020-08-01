<?php
namespace PyAngelo\Controllers\Lessons;

use Carbon\Carbon;
use Framework\{Request, Response};
use Framework\Contracts\PurifyContract;
use Framework\Contracts\AvatarContract;
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;
use PyAngelo\Repositories\SketchRepository;
use PyAngelo\Utilities\SketchFiles;

class LessonsShowController extends Controller {
  protected $tutorialRepository;
  protected $purifier;
  protected $avatar;
  protected $showCommentCount;
  protected $sketchRepository;
  protected $sketchFiles;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    TutorialRepository $tutorialRepository,
    PurifyContract $purifier,
    AvatarContract $avatar,
    $showCommentCount,
    SketchRepository $sketchRepository,
    SketchFiles $sketchFiles
  ) {
    parent::__construct($request, $response, $auth);
    $this->tutorialRepository = $tutorialRepository;
    $this->purifier = $purifier;
    $this->avatar = $avatar;
    $this->showCommentCount = $showCommentCount;
    $this->sketchRepository = $sketchRepository;
    $this->sketchFiles = $sketchFiles;
  }

  public function exec() {
    if (! $lesson = $this->getLessonFromSlugs())
      return $this->redirectToPageNotFound();

    // Set for redirect after login
    $this->request->session['redirect'] = $this->request->server['REQUEST_URI'];

    if ($this->premiumLessonAndNotAuthorised($lesson))
      return $this->displayBecomeAPremiumMemberPage($lesson);

    if ($this->freeLessonAndNotLoggedIn($lesson))
      return $this->displayBecomeAFreeMemberPage($lesson);

    $alertUser = $this->isUserReceivingAlerts($lesson);

    $lessons = $this->tutorialRepository->getTutorialLessons(
      $lesson['tutorial_id'],
      $this->auth->personId()
    );

    $captions = $this->tutorialRepository->getLessonCaptions(
      $lesson['tutorial_id'],
      $lesson['lesson_slug']
    );

    if (! $tutorial = $this->getTutorialFromSlugWithStats())
      return $this->redirectToPageNotFound();

    $comments = $this->tutorialRepository->getPublishedLessonComments($lesson['lesson_id']);
    foreach ($comments as &$comment) {
      $comment['created_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $comment['created_at'])->diffForHumans();
    }

    $sketch = $this->getOrCreateSketch($lesson);

    $this->response->setView('lessons/show.html.php');
    $this->response->setVars(array(
      'pageTitle' => $lesson['lesson_title'] . ' | ' . $lesson['tutorial_title'] . ' | PyAngelo',
      'metaDescription' => $lesson['lesson_description'],
      'activeLink' => 'Tutorials',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'tutorial' => $tutorial,
      'lesson' => $lesson,
      'alertUser' => $alertUser,
      'lessons' => $lessons,
      'captions' => $captions,
      'comments' => $comments,
      'purifier' => $this->purifier,
      'avatar' => $this->avatar,
      'showCommentCount' => $this->showCommentCount,
      'sketch' => $sketch
    ));
    return $this->response;
  }

  private function getLessonFromSlugs() {
    if (! isset($this->request->get['slug']) ||
        ! isset($this->request->get['lesson_slug'])
    ) {
      return false;
    }

    return $this->tutorialRepository->getLessonBySlugsWithStatus(
      $this->request->get['slug'],
      $this->request->get['lesson_slug'],
      $this->auth->personId()
    );
  }

  private function getTutorialFromSlugWithStats() {
    return $this->tutorialRepository->getTutorialBySlugWithStats(
      $this->request->get['slug'],
      $this->auth->personId()
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

  private function redirectToPageNotFound() {
    $this->response->header('Location: /page-not-found');
    return $this->response;
  }

  private function displayBecomeAPremiumMemberPage($lesson) {
    $this->response->setView('lessons/become-a-premium-member.html.php');
    $this->response->setVars(array(
      'pageTitle' => $lesson['lesson_title'] . ' | ' . $lesson['tutorial_title'] . ' | PyAngelo',
      'metaDescription' => $lesson['lesson_description'],
      'activeLink' => 'Tutorials',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'lesson' => $lesson
    ));
    return $this->response;
  }

  private function displayBecomeAFreeMemberPage($lesson) {
    $this->response->setView('lessons/become-a-free-member.html.php');
    $this->response->setVars(array(
      'pageTitle' => $lesson['lesson_title'] . ' | ' . $lesson['tutorial_title'] . ' | PyAngelo',
      'metaDescription' => $lesson['lesson_description'],
      'activeLink' => 'Tutorials',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'lesson' => $lesson
    ));
    return $this->response;
  }

  private function isUserReceivingAlerts($lesson) {
    $alertUser = false;
    if ($this->auth->loggedIn()) {
      $alertUser = $this->tutorialRepository->shouldUserReceiveAlert(
        $lesson['lesson_id'],
        $this->auth->personId()
      ) ? true : false;
    }
    return $alertUser;
  }

  private function getOrCreateSketch($lesson) {
    if (! $this->auth->loggedIn())
      return NULL;

    if ($lesson['single_sketch'])
      return $this->getOrCreateTutorialSketch($lesson);
    else
      return $this->getOrCreateLessonSketch($lesson);
  }

  private function getOrCreateTutorialSketch($lesson) {
    $sketch = $this->sketchRepository->getSketchByPersonAndTutorial(
      $this->auth->personId(),
      $lesson['tutorial_id']
    );
    if (!$sketch) {
      $sketchId = $this->sketchRepository->forkSketch(
        $lesson['tutorial_sketch_id'],
        $this->auth->personId(),
        $lesson['tutorial_title'],
        NULL,
        $lesson['tutorial_id']
      );

      if (!$sketchId) {
        // Create default sketch
        $sketchId = $this->sketchRepository->createNewSketch(
          $this->auth->personId(),
          $lesson['tutorial_title'],
          NULL,
          $lesson['tutorial_id']
        );

        $this->sketchFiles->createNewMain($sketchId);
      }
      else {
        $sketchFiles = $this->sketchRepository->getSketchFiles($sketchId);
        $this->sketchFiles->forkSketch(
          $lesson['tutorial_sketch_id'],
          $sketchId,
          $sketchFiles
        );
      }
      $sketch = $this->sketchRepository->getSketchById($sketchId);
    }
    return $sketch;
  }

  private function getOrCreateLessonSketch($lesson) {
    $sketch = $this->sketchRepository->getSketchByPersonAndLesson(
      $this->auth->personId(),
      $lesson['lesson_id']
    );
    if (!$sketch) {
      $sketchId = $this->sketchRepository->forkSketch(
        $lesson['lesson_sketch_id'],
        $this->auth->personId(),
        $lesson['lesson_title'],
        $lesson['lesson_id'],
        NULL
      );

      if (!$sketchId) {
        // Create default sketch
        $sketchId = $this->sketchRepository->createNewSketch(
          $this->auth->personId(),
          $lesson['lesson_title'],
          $lesson['lesson_id'],
          NULL
        );

        $this->sketchFiles->createNewMain($sketchId);
      }
      else {
        $sketchFiles = $this->sketchRepository->getSketchFiles($sketchId);
        $this->sketchFiles->forkSketch(
          $lesson['lesson_sketch_id'],
          $sketchId,
          $sketchFiles
        );
      }
      $sketch = $this->sketchRepository->getSketchById($sketchId);
    }
    return $sketch;
  }
}
