<?php
namespace Tests\src\PyAngelo\Controllers\Lessons;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Lessons\LessonsCommentController;

class LessonsCommentControllerTest extends TestCase {
  protected $tutorialRepository;
  protected $request;
  protected $response;
  protected $auth;
  protected $purifier;
  protected $avatar;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->purifier = Mockery::mock('Framework\Contracts\PurifyContract');
    $this->avatar = Mockery::mock('Framework\Contracts\AvatarContract');
    $this->controller = new LessonsCommentController (
      $this->request,
      $this->response,
      $this->auth,
      $this->tutorialRepository,
      $this->purifier,
      $this->avatar
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Lessons\LessonsCommentController');
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/lesson-comment.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must be logged in to add a comment."';
    $expectedCommentHtml = '"We could not add your comment due to errors."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $this->assertSame($expectedCommentHtml, $responseVars['commentHtml']);
  }

  public function testLessonCommentInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/lesson-comment.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"Please add a comment from the PyAngelo website."';
    $expectedCommentHtml = '"We could not add your comment due to errors."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $this->assertSame($expectedCommentHtml, $responseVars['commentHtml']);
  }

  public function testNoLessonId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/lesson-comment.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"Please add a comment to a lesson."';
    $expectedCommentHtml = '"We could not add your comment due to errors."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $this->assertSame($expectedCommentHtml, $responseVars['commentHtml']);
  }

  public function testLessonCommentNoComment() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->request->post['lessonId'] = 1;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/lesson-comment.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"A comment must contain some text."';
    $expectedCommentHtml = '"We could not add your comment due to errors."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $this->assertSame($expectedCommentHtml, $responseVars['commentHtml']);
  }

  public function testInvalidLesson() {
    $personId = 99;
    $lessonId = 1;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->tutorialRepository->shouldReceive('getLessonById')->once()->with($lessonId)->andReturn(NULL);
    $this->request->post['lessonId'] = $lessonId;
    $this->request->post['lessonComment'] = 'Great comment';

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/lesson-comment.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must add a comment to a valid lesson."';
    $expectedCommentHtml = '"We could not add your comment due to errors."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $this->assertSame($expectedCommentHtml, $responseVars['commentHtml']);
  }

  public function testLessonCommentValidComment() {
    $personId = 99;
    $email = 'any_email@hotmail.com';
    $person = [
      'person_id' => $personId,
      'given_name' => 'Fast',
      'family_name' => 'Fred',
      'email' => $email
    ];
    $lessonId = 1;
    $lesson = [
      'lesson_id' => $lessonId,
      'lesson_slug' => 'great-lesson',
      'lesson_title' => 'A Great Lesson',
      'tutorial_slug' => 'great-tutorial'
    ];
    $lessonComment = 'Great lesson';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('person')->times(4)->with()->andReturn($person);
    $this->tutorialRepository->shouldReceive('getLessonById')->once()->with($lessonId)->andReturn($lesson);
    $this->tutorialRepository->shouldReceive('insertLessonComment')->once();
    $this->tutorialRepository->shouldReceive('getFollowers')->once()->andReturn([]);
    $this->purifier->shouldReceive('purify')->once()->with($lessonComment)->andReturn($lessonComment);
    $this->avatar->shouldReceive('getAvatarUrl')->twice()->with($email)->andReturn($lessonComment);
    $this->avatar->shouldReceive('setSizeInPixels')->once()->with(25);
    $this->request->post['lessonId'] = $lessonId;
    $this->request->post['lessonComment'] = $lessonComment;
    $this->request->server['REQUEST_SCHEME'] = 'https';
    $this->request->server['SERVER_NAME'] = 'www.pyangelo.com';

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/lesson-comment.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Your comment has been added."';
    $expectedCommentHtml = '"    <div class=\"media\">\n      <div class=\"media-left\">\n        <img class=\"media-object\" src=\"Great lesson\" alt=\"Fast Fred\" \/>\n      <\/div>\n      <div class=\"media-body\">\n        <h4 class=\"media-heading\">Fast Fred <small><i>Posted now<\/i><\/small><\/h4>\n        <p>Great lesson<\/p>\n      <\/div>\n      <hr \/>\n    <\/div>"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $this->assertSame($expectedCommentHtml, $responseVars['commentHtml']);
  }
}
?>
