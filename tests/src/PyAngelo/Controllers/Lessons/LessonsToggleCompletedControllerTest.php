<?php
namespace Tests\src\PyAngelo\Controllers\Lessons;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Lessons\LessonsToggleCompletedController;

class LessonsToggleCompletedControllerTest extends TestCase {
  protected $tutorialRepository;
  protected $request;
  protected $response;
  protected $auth;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->controller = new LessonsToggleCompletedController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Lessons\LessonsToggleCompletedController');
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/toggle-completed.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('info', $responseVars['status']);
    $this->assertSame('Log in to record your progress', $responseVars['message']);
    $this->assertSame(0, $responseVars['percentComplete']);
  }

  public function testWhenNoLessonId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/toggle-completed.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must watch a lesson to complete it.', $responseVars['message']);
    $this->assertSame(0, $responseVars['percentComplete']);
  }

  public function testWhenNotRealLesson() {
    $this->request->post['lessonId'] = 100;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->tutorialRepository
      ->shouldReceive('getLessonById')
      ->once()
      ->with($this->request->post['lessonId'])
      ->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/toggle-completed.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must select a valid lesson to complete.', $responseVars['message']);
    $this->assertSame(0, $responseVars['percentComplete']);
  }

  public function testCompleteWhenAlreadyCompleted() {
    $lessonId = 100;
    $personId = 2;
    $percentComplete = 40;
    $lesson = [ 'lesson_id' => $lessonId ];
    $lessonComplete = [
      'person_id' => $personId,
      'lesson_id' => $lessonId,
      'completed_at' => '2016-11-01'
    ];
    $this->request->post['lessonId'] = $lessonId;
    $this->request->post['action'] = 'complete';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->tutorialRepository
      ->shouldReceive('getLessonById')
      ->once()
      ->with($this->request->post['lessonId'])
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('getLessonCompleted')
      ->once()
      ->with($personId, $this->request->post['lessonId'])
      ->andReturn($lessonComplete);
    $this->tutorialRepository
      ->shouldReceive('getTutorialPercentComplete')
      ->once()
      ->with($personId, $this->request->post['lessonId'])
      ->andReturn($percentComplete);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/toggle-completed.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('success', $responseVars['status']);
    $this->assertSame('Lesson marked as completed.', $responseVars['message']);
    $this->assertSame($percentComplete, $responseVars['percentComplete']);
  }

  public function testCompleteWhenNotCompleted() {
    $lessonId = 100;
    $personId = 2;
    $percentComplete = 40;
    $lesson = [ 'lesson_id' => $lessonId ];
    $this->request->post['lessonId'] = $lessonId;
    $this->request->post['action'] = 'complete';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(3)->with()->andReturn($personId);
    $this->tutorialRepository
      ->shouldReceive('getLessonById')
      ->once()
      ->with($this->request->post['lessonId'])
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('getLessonCompleted')
      ->once()
      ->with($personId, $this->request->post['lessonId'])
      ->andReturn(NULL);
    $this->tutorialRepository
      ->shouldReceive('insertLessonCompleted')
      ->once()
      ->with($personId, $this->request->post['lessonId']);
    $this->tutorialRepository
      ->shouldReceive('getTutorialPercentComplete')
      ->once()
      ->with($personId, $this->request->post['lessonId'])
      ->andReturn($percentComplete);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/toggle-completed.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('success', $responseVars['status']);
    $this->assertSame('Lesson marked as completed.', $responseVars['message']);
    $this->assertSame($percentComplete, $responseVars['percentComplete']);
  }

  public function testToggleToIncomplete() {
    $lessonId = 100;
    $personId = 2;
    $percentComplete = 40;
    $lesson = [ 'lesson_id' => $lessonId ];
    $lessonComplete = [
      'person_id' => $personId,
      'lesson_id' => $lessonId,
      'completed_at' => '2016-11-01'
    ];
    $this->request->post['lessonId'] = $lessonId;
    $this->request->post['action'] = 'toggle';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(3)->with()->andReturn($personId);
    $this->tutorialRepository
      ->shouldReceive('getLessonById')
      ->once()
      ->with($this->request->post['lessonId'])
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('getLessonCompleted')
      ->once()
      ->with($personId, $this->request->post['lessonId'])
      ->andReturn($lessonComplete);
    $this->tutorialRepository
      ->shouldReceive('deleteLessonCompleted')
      ->once()
      ->with($personId, $this->request->post['lessonId']);
    $this->tutorialRepository
      ->shouldReceive('getTutorialPercentComplete')
      ->once()
      ->with($personId, $this->request->post['lessonId'])
      ->andReturn($percentComplete);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/toggle-completed.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('info', $responseVars['status']);
    $this->assertSame('Lesson marked as incomplete.', $responseVars['message']);
    $this->assertSame($percentComplete, $responseVars['percentComplete']);
  }

  public function testToggleToComplete() {
    $lessonId = 100;
    $personId = 2;
    $percentComplete = 40;
    $lesson = [ 'lesson_id' => $lessonId ];
    $this->request->post['lessonId'] = $lessonId;
    $this->request->post['action'] = 'toggle';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(3)->with()->andReturn($personId);
    $this->tutorialRepository
      ->shouldReceive('getLessonById')
      ->once()
      ->with($this->request->post['lessonId'])
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('getLessonCompleted')
      ->once()
      ->with($personId, $this->request->post['lessonId'])
      ->andReturn(NULL);
    $this->tutorialRepository
      ->shouldReceive('insertLessonCompleted')
      ->once()
      ->with($personId, $this->request->post['lessonId']);
    $this->tutorialRepository
      ->shouldReceive('getTutorialPercentComplete')
      ->once()
      ->with($personId, $this->request->post['lessonId'])
      ->andReturn($percentComplete);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/toggle-completed.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('success', $responseVars['status']);
    $this->assertSame('Lesson marked as completed.', $responseVars['message']);
    $this->assertSame($percentComplete, $responseVars['percentComplete']);
  }
}
?>
