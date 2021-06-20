<?php
namespace Tests\src\PyAngelo\Controllers\Lessons;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Lessons\LessonsToggleAlertController;

class LessonsToggleAlertControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->controller = new LessonsToggleAlertController (
      $this->request,
      $this->response,
      $this->auth,
      $this->tutorialRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Lessons\LessonsToggleAlertController');
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('info', $responseVars['status']);
    $this->assertSame('Log in to update your notifications', $responseVars['message']);
  }

  public function testWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('Please update your notifications from the PyAngelo website.', $responseVars['message']);
  }

  public function testWhenNoLessonId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must select a lesson to be notified about.', $responseVars['message']);
  }

  public function testWhenNotRealLesson() {
    $this->request->post['lessonId'] = 100;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->tutorialRepository
      ->shouldReceive('getLessonById')
      ->once()
      ->with($this->request->post['lessonId'])
      ->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must select a valid lesson to be notified about.', $responseVars['message']);
  }

  public function testToggleAlertWhenAlreadyAlerted() {
    $lessonId = 100;
    $personId = 2;
    $lesson = [ 'lesson_id' => $lessonId ];
    $this->request->post['lessonId'] = $lessonId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->tutorialRepository
      ->shouldReceive('getLessonById')
      ->once()
      ->with($this->request->post['lessonId'])
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('shouldUserReceiveAlert')
      ->once()
      ->with($this->request->post['lessonId'], $personId)
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('removeFromLessonAlert')
      ->once()
      ->with($this->request->post['lessonId'], $personId);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('info', $responseVars['status']);
    $this->assertSame('Notifications are off for this lesson', $responseVars['message']);
  }

  public function testToggleAlertWhenNotAlerted() {
    $lessonId = 100;
    $personId = 2;
    $lesson = [ 'lesson_id' => $lessonId ];
    $this->request->post['lessonId'] = $lessonId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->tutorialRepository
      ->shouldReceive('getLessonById')
      ->once()
      ->with($this->request->post['lessonId'])
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('shouldUserReceiveAlert')
      ->once()
      ->with($this->request->post['lessonId'], $personId)
      ->andReturn(NULL);
    $this->tutorialRepository
      ->shouldReceive('addToLessonAlert')
      ->once()
      ->with($this->request->post['lessonId'], $personId);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('success', $responseVars['status']);
    $this->assertSame('Notifications are on for this lesson', $responseVars['message']);
  }
}
?>
