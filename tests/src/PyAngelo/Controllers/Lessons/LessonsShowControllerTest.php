<?php
namespace Tests\src\PyAngelo\Controllers\Lessons;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Lessons\LessonsShowController;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class LessonsShowControllerTest extends TestCase {
  protected $tutorialRepository;
  protected $sketchRepository;
  protected $request;
  protected $response;
  protected $auth;
  protected $purifier;
  protected $avatar;
  protected $showCommentCount;
  protected $sketchFiles;
  protected $ownerOfStarterSketchesId;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->purifier = Mockery::mock('Framework\Contracts\PurifyContract');
    $this->avatar = Mockery::mock('Framework\Contracts\AvatarContract');
    $this->showCommentCount = 5;
    $this->sketchRepository = Mockery::mock('PyAngelo\Repositories\SketchRepository');
    $this->sketchFiles = Mockery::mock('PyAngelo\Utilities\SketchFiles');
    $this->ownerOfStarterSketchesId = 1;
    $this->controller = new LessonsShowController (
      $this->request,
      $this->response,
      $this->auth,
      $this->tutorialRepository,
      $this->purifier,
      $this->avatar,
      $this->showCommentCount,
      $this->sketchRepository,
      $this->sketchFiles,
      $this->ownerOfStarterSketchesId
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Lessons\LessonsShowController');
  }

  public function testLessonsShowInvalidSlugs() {
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  #[RunInSeparateProcess]
  public function testLessonsShowPremiumLessonPersonNotPremium() {
    session_start();
    $this->request->server['REQUEST_URI'] = 'some-url';
    $tutorialSlug = 'coding-magic';
    $lessonSlug = 'coding-introduction';
    $lessonTitle = 'Coding Introduction';
    $lessonDescription = 'Learn what coding is.';
    $tutorialTitle = 'Coding Magic';
    $premiumSecurityId = 3;
    $lessonId = 1;
    $lesson = [
      'lesson_id' => $lessonId,
      'lesson_title' => $lessonTitle,
      'lesson_description' => $lessonDescription,
      'tutorial_title' => $tutorialTitle,
      'lesson_slug' => $lessonSlug,
      'lesson_security_level_id' => $premiumSecurityId,
      'youtube_url' => 'test-youtube-url',
      'display_order' => 1
    ];
    $this->request->get['slug'] = $tutorialSlug;
    $this->request->get['lesson_slug'] = $lessonSlug;
    $this->auth->shouldReceive('isPremium')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn(0);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository
      ->shouldReceive('getLessonBySlugsWithStatus')
      ->once()
      ->with($tutorialSlug, $lessonSlug, 0)
      ->andReturn($lesson);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/become-a-premium-member.html.php';
    $expectedPageTitle = $lessonTitle . ' | ' . $tutorialTitle . ' | PyAngelo';
    $expectedMetaDescription = $lessonDescription;
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  #[RunInSeparateProcess]
  public function testLessonsShowFreeMemberLessonPersonNotLoggedIn() {
    session_start();
    $this->request->server['REQUEST_URI'] = 'some-url';
    $tutorialSlug = 'f2l-magic';
    $lessonSlug = 'f2l-introduction';
    $lessonTitle = 'F2L Introduction';
    $lessonDescription = 'Learn what the F2L is.';
    $tutorialTitle = 'F2L Magic';
    $freeMemberSecurityId = 2;
    $lesson = [
      'lesson_id' => 1,
      'lesson_title' => $lessonTitle,
      'lesson_description' => $lessonDescription,
      'tutorial_title' => $tutorialTitle,
      'lesson_slug' => $lessonSlug,
      'lesson_security_level_id' => $freeMemberSecurityId,
      'youtube_url' => 'test-youtube-url',
      'display_order' => 1
    ];
    $this->request->get['slug'] = $tutorialSlug;
    $this->request->get['lesson_slug'] = $lessonSlug;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn(0);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository
      ->shouldReceive('getLessonBySlugsWithStatus')
      ->once()
      ->with($tutorialSlug, $lessonSlug, 0)
      ->andReturn($lesson);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/become-a-free-member.html.php';
    $expectedPageTitle = $lessonTitle . ' | ' . $tutorialTitle . ' | PyAngelo';
    $expectedMetaDescription = $lessonDescription;
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  #[RunInSeparateProcess]
  public function testLessonsShowShowAnyoneVideoWithExistingSketchWithoutSingleSketch() {
    session_start();
    $personId = 2;
    $sketchId = 99;
    $this->request->server['REQUEST_URI'] = 'some-url';
    $tutorialSlug = 'f2l-magic';
    $lessonSlug = 'f2l-introduction';
    $lessonTitle = 'F2L Introduction';
    $lessonDescription = 'Learn what the F2L is.';
    $videoName = 'f2l-introduction.mp4';
    $tutorialId = 10;
    $singleSketch = 0;
    $tutorialTitle = 'F2L Magic';
    $anyoneSecurityId = 1;
    $lessonId = 1;
    $lesson = [
      'lesson_id' => $lessonId,
      'lesson_title' => $lessonTitle,
      'lesson_description' => $lessonDescription,
      'video_name' => $videoName,
      'tutorial_id' => $tutorialId,
      'single_sketch' => $singleSketch,
      'tutorial_title' => $tutorialTitle,
      'lesson_slug' => $lessonSlug,
      'lesson_security_level_id' => $anyoneSecurityId,
      'youtube_url' => 'test-youtube-url',
      'display_order' => 1
    ];
    $lessons = [
      [
        'lesson_title' => 'First Lesson',
        'display_duration' => '1:23',
        'completed' => 1
      ],
      [
        'lesson_title' => 'Second Lesson',
        'display_duration' => '2:15',
        'completed' => 0
      ]
    ];
    $sketch = [
      'sketch_id' => $sketchId,
      'person_id' => $personId,
      'lesson_id' => $lessonId,
      'tutorial_id' => NULL,
      'title' => $lessonSlug
    ];
    $comments = [];
    $this->request->get['slug'] = $tutorialSlug;
    $this->request->get['lesson_slug'] = $lessonSlug;
    $this->auth->shouldReceive('personId')->times(6)->with()->andReturn($personId);
    $this->auth->shouldReceive('loggedIn')->twice()->with()->andReturn(TRUE);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository
      ->shouldReceive('getLessonBySlugsWithStatus')
      ->once()
      ->with($tutorialSlug, $lessonSlug, $personId)
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('getLessonCaptions')
      ->once()
      ->with($tutorialId, $lessonSlug)
      ->andReturn($comments);
    $this->tutorialRepository
      ->shouldReceive('getTutorialBySlugWithStats')
      ->once()
      ->with($tutorialSlug, $personId)
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('getTutorialLessons')
      ->once()
      ->with($tutorialId, $personId)
      ->andReturn($lessons);
    $this->tutorialRepository
      ->shouldReceive('getPublishedLessonComments')
      ->once()
      ->with($lessonId)
      ->andReturn($comments);
    $this->tutorialRepository
      ->shouldReceive('shouldUserReceiveAlert')
      ->once()
      ->with($lessonId, $personId)
      ->andReturn(FALSE);
    $this->sketchRepository
      ->shouldReceive('getSketchByPersonAndLesson')
      ->once()
      ->with($personId, $lessonId)
      ->andReturn($sketch);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/show.html.php';
    $expectedPageTitle = $lessonTitle . ' | ' . $tutorialTitle . ' | PyAngelo';
    $expectedMetaDescription = $lessonDescription;
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  #[RunInSeparateProcess]
  public function testLessonsShowShowAnyoneVideoWithExistingSketchWithSingleSketch() {
    session_start();
    $personId = 2;
    $sketchId = 99;
    $this->request->server['REQUEST_URI'] = 'some-url';
    $tutorialSlug = 'f2l-magic';
    $lessonSlug = 'f2l-introduction';
    $lessonTitle = 'F2L Introduction';
    $lessonDescription = 'Learn what the F2L is.';
    $videoName = 'f2l-introduction.mp4';
    $tutorialId = 10;
    $singleSketch = 1;
    $tutorialTitle = 'F2L Magic';
    $anyoneSecurityId = 1;
    $lessonId = 1;
    $lesson = [
      'lesson_id' => $lessonId,
      'lesson_title' => $lessonTitle,
      'lesson_description' => $lessonDescription,
      'video_name' => $videoName,
      'tutorial_id' => $tutorialId,
      'single_sketch' => $singleSketch,
      'tutorial_title' => $tutorialTitle,
      'lesson_slug' => $lessonSlug,
      'lesson_security_level_id' => $anyoneSecurityId,
      'youtube_url' => 'test-youtube-url',
      'display_order' => 1
    ];
    $lessons = [
      [
        'lesson_title' => 'First Lesson',
        'display_duration' => '1:23',
        'completed' => 1
      ],
      [
        'lesson_title' => 'Second Lesson',
        'display_duration' => '2:15',
        'completed' => 0
      ]
    ];
    $sketch = [
      'sketch_id' => $sketchId,
      'person_id' => $personId,
      'lesson_id' => $lessonId,
      'tutorial_id' => NULL,
      'title' => $lessonSlug
    ];
    $comments = [];
    $this->request->get['slug'] = $tutorialSlug;
    $this->request->get['lesson_slug'] = $lessonSlug;
    $this->auth->shouldReceive('personId')->times(6)->with()->andReturn($personId);
    $this->auth->shouldReceive('loggedIn')->twice()->with()->andReturn(TRUE);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository
      ->shouldReceive('getLessonBySlugsWithStatus')
      ->once()
      ->with($tutorialSlug, $lessonSlug, $personId)
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('getLessonCaptions')
      ->once()
      ->with($tutorialId, $lessonSlug)
      ->andReturn($comments);
    $this->tutorialRepository
      ->shouldReceive('getTutorialBySlugWithStats')
      ->once()
      ->with($tutorialSlug, $personId)
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('getTutorialLessons')
      ->once()
      ->with($tutorialId, $personId)
      ->andReturn($lessons);
    $this->tutorialRepository
      ->shouldReceive('getPublishedLessonComments')
      ->once()
      ->with($lessonId)
      ->andReturn($comments);
    $this->tutorialRepository
      ->shouldReceive('shouldUserReceiveAlert')
      ->once()
      ->with($lessonId, $personId)
      ->andReturn(FALSE);
    $this->sketchRepository
      ->shouldReceive('getSketchByPersonAndTutorial')
      ->once()
      ->with($personId, $tutorialId)
      ->andReturn($sketch);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/show.html.php';
    $expectedPageTitle = $lessonTitle . ' | ' . $tutorialTitle . ' | PyAngelo';
    $expectedMetaDescription = $lessonDescription;
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  #[RunInSeparateProcess]
  public function testLessonsShowShowAnyoneVideoWithNoSketchNoCreateWithoutSingleSketch() {
    session_start();
    $personId = 2;
    $sketchId = 99;
    $this->request->server['REQUEST_URI'] = 'some-url';
    $tutorialSlug = 'f2l-magic';
    $lessonSlug = 'f2l-introduction';
    $lessonTitle = 'F2L Introduction';
    $lessonDescription = 'Learn what the F2L is.';
    $videoName = 'f2l-introduction.mp4';
    $tutorialId = 10;
    $singleSketch = 0;
    $tutorialTitle = 'F2L Magic';
    $anyoneSecurityId = 1;
    $lessonId = 1;
    $lesson = [
      'lesson_id' => $lessonId,
      'lesson_title' => $lessonTitle,
      'lesson_description' => $lessonDescription,
      'video_name' => $videoName,
      'tutorial_id' => $tutorialId,
      'single_sketch' => $singleSketch,
      'lesson_sketch_id' => $sketchId,
      'tutorial_title' => $tutorialTitle,
      'lesson_slug' => $lessonSlug,
      'lesson_security_level_id' => $anyoneSecurityId,
      'youtube_url' => 'test-youtube-url',
      'display_order' => 1
    ];
    $lessons = [
      [
        'lesson_title' => 'First Lesson',
        'display_duration' => '1:23',
        'completed' => 1
      ],
      [
        'lesson_title' => 'Second Lesson',
        'display_duration' => '2:15',
        'completed' => 0
      ]
    ];
    $sketch = [
      'sketch_id' => $sketchId,
      'person_id' => $personId,
      'lesson_id' => $lessonId,
      'title' => $lessonSlug
    ];
    $newSketchId = 2000;
    $newSketch = [
      'sketch_id' => $newSketchId,
      'person_id' => $personId,
      'lesson_id' => $lessonId,
      'title' => $lessonSlug
    ];
    $files = [
      'name' => 'main.py'
    ];
    $comments = [];
    $this->request->get['slug'] = $tutorialSlug;
    $this->request->get['lesson_slug'] = $lessonSlug;
    $this->auth->shouldReceive('personId')->times(6)->with()->andReturn($personId);
    $this->auth->shouldReceive('loggedIn')->twice()->with()->andReturn(TRUE);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository
      ->shouldReceive('getLessonBySlugsWithStatus')
      ->once()
      ->with($tutorialSlug, $lessonSlug, $personId)
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('getLessonCaptions')
      ->once()
      ->with($tutorialId, $lessonSlug)
      ->andReturn($comments);
    $this->tutorialRepository
      ->shouldReceive('getTutorialBySlugWithStats')
      ->once()
      ->with($tutorialSlug, $personId)
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('getTutorialLessons')
      ->once()
      ->with($tutorialId, $personId)
      ->andReturn($lessons);
    $this->tutorialRepository
      ->shouldReceive('getPublishedLessonComments')
      ->once()
      ->with($lessonId)
      ->andReturn($comments);
    $this->tutorialRepository
      ->shouldReceive('shouldUserReceiveAlert')
      ->once()
      ->with($lessonId, $personId)
      ->andReturn(FALSE);
    $this->sketchRepository
      ->shouldReceive('getSketchByPersonAndLesson')
      ->once()
      ->with($personId, $lessonId)
      ->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/show.html.php';
    $expectedPageTitle = $lessonTitle . ' | ' . $tutorialTitle . ' | PyAngelo';
    $expectedMetaDescription = $lessonDescription;
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  #[RunInSeparateProcess]
  public function testLessonsShowShowAnyoneVideoWithNoSketchWithCreateWithoutSingleSketch() {
    session_start();
    $personId = 2;
    $sketchId = 99;
    $this->request->server['REQUEST_URI'] = 'some-url';
    $tutorialSlug = 'f2l-magic';
    $lessonSlug = 'f2l-introduction';
    $lessonTitle = 'F2L Introduction';
    $lessonDescription = 'Learn what the F2L is.';
    $videoName = 'f2l-introduction.mp4';
    $tutorialId = 10;
    $singleSketch = 0;
    $tutorialTitle = 'F2L Magic';
    $anyoneSecurityId = 1;
    $lessonId = 1;
    $lesson = [
      'lesson_id' => $lessonId,
      'lesson_title' => $lessonTitle,
      'lesson_description' => $lessonDescription,
      'video_name' => $videoName,
      'tutorial_id' => $tutorialId,
      'single_sketch' => $singleSketch,
      'lesson_sketch_id' => $sketchId,
      'tutorial_title' => $tutorialTitle,
      'lesson_slug' => $lessonSlug,
      'lesson_security_level_id' => $anyoneSecurityId,
      'youtube_url' => 'test-youtube-url',
      'display_order' => 1
    ];
    $lessons = [
      [
        'lesson_title' => 'First Lesson',
        'display_duration' => '1:23',
        'completed' => 1
      ],
      [
        'lesson_title' => 'Second Lesson',
        'display_duration' => '2:15',
        'completed' => 0
      ]
    ];
    $sketch = [
      'sketch_id' => $sketchId,
      'person_id' => $personId,
      'lesson_id' => $lessonId,
      'title' => $lessonSlug
    ];
    $newSketchId = 2000;
    $newSketch = [
      'sketch_id' => $newSketchId,
      'person_id' => $personId,
      'lesson_id' => $lessonId,
      'title' => $lessonSlug
    ];
    $files = [
      'name' => 'main.py'
    ];
    $comments = [];
    $this->request->get['slug'] = $tutorialSlug;
    $this->request->get['lesson_slug'] = $lessonSlug;
    $this->request->get['create-sketch'] = 1;
    $this->auth->shouldReceive('personId')->times(8)->with()->andReturn($personId);
    $this->auth->shouldReceive('loggedIn')->twice()->with()->andReturn(TRUE);
    $this->tutorialRepository
      ->shouldReceive('getLessonBySlugsWithStatus')
      ->once()
      ->with($tutorialSlug, $lessonSlug, $personId)
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('getLessonCaptions')
      ->once()
      ->with($tutorialId, $lessonSlug)
      ->andReturn($comments);
    $this->tutorialRepository
      ->shouldReceive('getTutorialBySlugWithStats')
      ->once()
      ->with($tutorialSlug, $personId)
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('getTutorialLessons')
      ->once()
      ->with($tutorialId, $personId)
      ->andReturn($lessons);
    $this->tutorialRepository
      ->shouldReceive('getPublishedLessonComments')
      ->once()
      ->with($lessonId)
      ->andReturn($comments);
    $this->tutorialRepository
      ->shouldReceive('shouldUserReceiveAlert')
      ->once()
      ->with($lessonId, $personId)
      ->andReturn(FALSE);
    $this->sketchRepository
      ->shouldReceive('getSketchByPersonAndLesson')
      ->once()
      ->with($personId, $lessonId)
      ->andReturn(NULL);
    $this->sketchRepository
      ->shouldReceive('forkSketch')
      ->once()
      ->with($sketchId, $personId, $lessonTitle, $lessonId, NULL)
      ->andReturn($newSketchId);
    $this->sketchRepository
      ->shouldReceive('getSketchFiles')
      ->once()
      ->with($newSketchId)
      ->andReturn($files);
    $this->sketchRepository
      ->shouldReceive('getSketchById')
      ->once()
      ->with($sketchId)
      ->andReturn($sketch);
    $this->sketchRepository
      ->shouldReceive('getSketchById')
      ->once()
      ->with($newSketchId)
      ->andReturn($newSketch);
    $this->sketchFiles
      ->shouldReceive('forkSketch')
      ->once()
      ->with($sketch, $personId, $newSketchId, $files);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /tutorials/f2l-magic/f2l-introduction';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  #[RunInSeparateProcess]
  public function testLessonsShowShowAnyoneVideoWithNoSketchNoCreateWithSingleSketch() {
    session_start();
    $sketchPersonId = 2;
    $sketchId = 99;
    $this->request->server['REQUEST_URI'] = 'some-url';
    $tutorialSlug = 'f2l-magic';
    $lessonSlug = 'f2l-introduction';
    $lessonTitle = 'F2L Introduction';
    $lessonDescription = 'Learn what the F2L is.';
    $videoName = 'f2l-introduction.mp4';
    $tutorialId = 10;
    $singleSketch = 1;
    $tutorialTitle = 'F2L Magic';
    $anyoneSecurityId = 1;
    $lessonId = 1;
    $lesson = [
      'lesson_id' => $lessonId,
      'lesson_title' => $lessonTitle,
      'lesson_description' => $lessonDescription,
      'video_name' => $videoName,
      'tutorial_id' => $tutorialId,
      'single_sketch' => $singleSketch,
      'tutorial_title' => $tutorialTitle,
      'lesson_slug' => $lessonSlug,
      'lesson_security_level_id' => $anyoneSecurityId,
      'tutorial_sketch_id' => $sketchId,
      'youtube_url' => 'test-youtube-url',
      'display_order' => 1
    ];
    $lessons = [
      [
        'lesson_title' => 'First Lesson',
        'display_duration' => '1:23',
        'completed' => 1
      ],
      [
        'lesson_title' => 'Second Lesson',
        'display_duration' => '2:15',
        'completed' => 0
      ]
    ];
    $sketch = [
      'sketch_id' => $sketchId,
      'person_id' => $sketchPersonId,
      'lesson_id' => $lessonId,
      'title' => $lessonSlug
    ];
    $newPersonId = 3001;
    $newSketchId = 4001;
    $newSketch = [
      'sketch_id' => $newSketchId,
      'person_id' => $newPersonId,
      'lesson_id' => $lessonId,
      'title' => $lessonSlug
    ];
    $files = [
      'name' => 'main.py'
    ];
    $comments = [];
    $this->request->get['slug'] = $tutorialSlug;
    $this->request->get['lesson_slug'] = $lessonSlug;
    $this->auth->shouldReceive('personId')->times(6)->with()->andReturn($newPersonId);
    $this->auth->shouldReceive('loggedIn')->twice()->with()->andReturn(TRUE);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository
      ->shouldReceive('getLessonBySlugsWithStatus')
      ->once()
      ->with($tutorialSlug, $lessonSlug, $newPersonId)
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('getLessonCaptions')
      ->once()
      ->with($tutorialId, $lessonSlug)
      ->andReturn($comments);
    $this->tutorialRepository
      ->shouldReceive('getTutorialBySlugWithStats')
      ->once()
      ->with($tutorialSlug, $newPersonId)
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('getTutorialLessons')
      ->once()
      ->with($tutorialId, $newPersonId)
      ->andReturn($lessons);
    $this->tutorialRepository
      ->shouldReceive('getPublishedLessonComments')
      ->once()
      ->with($lessonId)
      ->andReturn($comments);
    $this->tutorialRepository
      ->shouldReceive('shouldUserReceiveAlert')
      ->once()
      ->with($lessonId, $newPersonId)
      ->andReturn(FALSE);
    $this->sketchRepository
      ->shouldReceive('getSketchByPersonAndTutorial')
      ->once()
      ->with($newPersonId, $tutorialId)
      ->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/show.html.php';
    $expectedPageTitle = $lessonTitle . ' | ' . $tutorialTitle . ' | PyAngelo';
    $expectedMetaDescription = $lessonDescription;
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  #[RunInSeparateProcess]
  public function testLessonsShowShowAnyoneVideoWithNoSketchWithCreateWithSingleSketch() {
    session_start();
    $sketchPersonId = 2;
    $sketchId = 99;
    $this->request->server['REQUEST_URI'] = 'some-url';
    $tutorialSlug = 'f2l-magic';
    $lessonSlug = 'f2l-introduction';
    $lessonTitle = 'F2L Introduction';
    $lessonDescription = 'Learn what the F2L is.';
    $videoName = 'f2l-introduction.mp4';
    $tutorialId = 10;
    $singleSketch = 1;
    $tutorialTitle = 'F2L Magic';
    $anyoneSecurityId = 1;
    $lessonId = 1;
    $lesson = [
      'lesson_id' => $lessonId,
      'lesson_title' => $lessonTitle,
      'lesson_description' => $lessonDescription,
      'video_name' => $videoName,
      'tutorial_id' => $tutorialId,
      'single_sketch' => $singleSketch,
      'tutorial_title' => $tutorialTitle,
      'lesson_slug' => $lessonSlug,
      'lesson_security_level_id' => $anyoneSecurityId,
      'tutorial_sketch_id' => $sketchId,
      'youtube_url' => 'test-youtube-url',
      'display_order' => 1
    ];
    $lessons = [
      [
        'lesson_title' => 'First Lesson',
        'display_duration' => '1:23',
        'completed' => 1
      ],
      [
        'lesson_title' => 'Second Lesson',
        'display_duration' => '2:15',
        'completed' => 0
      ]
    ];
    $sketch = [
      'sketch_id' => $sketchId,
      'person_id' => $sketchPersonId,
      'lesson_id' => $lessonId,
      'title' => $lessonSlug
    ];
    $newPersonId = 3001;
    $newSketchId = 4001;
    $newSketch = [
      'sketch_id' => $newSketchId,
      'person_id' => $newPersonId,
      'lesson_id' => $lessonId,
      'title' => $lessonSlug
    ];
    $files = [
      'name' => 'main.py'
    ];
    $comments = [];
    $this->request->get['slug'] = $tutorialSlug;
    $this->request->get['lesson_slug'] = $lessonSlug;
    $this->request->get['create-sketch'] = 1;
    $this->auth->shouldReceive('personId')->times(8)->with()->andReturn($newPersonId);
    $this->auth->shouldReceive('loggedIn')->twice()->with()->andReturn(TRUE);
    $this->tutorialRepository
      ->shouldReceive('getLessonBySlugsWithStatus')
      ->once()
      ->with($tutorialSlug, $lessonSlug, $newPersonId)
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('getLessonCaptions')
      ->once()
      ->with($tutorialId, $lessonSlug)
      ->andReturn($comments);
    $this->tutorialRepository
      ->shouldReceive('getTutorialBySlugWithStats')
      ->once()
      ->with($tutorialSlug, $newPersonId)
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('getTutorialLessons')
      ->once()
      ->with($tutorialId, $newPersonId)
      ->andReturn($lessons);
    $this->tutorialRepository
      ->shouldReceive('getPublishedLessonComments')
      ->once()
      ->with($lessonId)
      ->andReturn($comments);
    $this->tutorialRepository
      ->shouldReceive('shouldUserReceiveAlert')
      ->once()
      ->with($lessonId, $newPersonId)
      ->andReturn(FALSE);
    $this->sketchRepository
      ->shouldReceive('getSketchByPersonAndTutorial')
      ->once()
      ->with($newPersonId, $tutorialId)
      ->andReturn(NULL);
    $this->sketchRepository
      ->shouldReceive('forkSketch')
      ->once()
      ->with($sketchId, $newPersonId, $tutorialTitle, NULL, $tutorialId)
      ->andReturn($newSketchId);
    $this->sketchRepository
      ->shouldReceive('getSketchFiles')
      ->once()
      ->with($newSketchId)
      ->andReturn($files);
    $this->sketchRepository
      ->shouldReceive('getSketchById')
      ->once()
      ->with($sketchId)
      ->andReturn($sketch);
    $this->sketchRepository
      ->shouldReceive('getSketchById')
      ->once()
      ->with($newSketchId)
      ->andReturn($newSketch);
    $this->sketchFiles
      ->shouldReceive('forkSketch')
      ->once()
      ->with($sketch, $newPersonId, $newSketchId, $files);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /tutorials/f2l-magic/f2l-introduction';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());

  }
}
?>
