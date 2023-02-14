<?php
namespace Tests\src\PyAngelo\Controllers\Lessons;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Lessons\LessonsToggleFavouritedController;

class LessonsToggleFavouritedControllerTest extends TestCase {
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
    $this->controller = new LessonsToggleFavouritedController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Lessons\LessonsToggleFavouritedController');
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/toggle-favourited.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('info', $responseVars['status']);
    $this->assertSame('Log in to record your progress', $responseVars['message']);
  }

  public function testWhenNoLessonId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/toggle-favourited.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must select a lesson to favourite.', $responseVars['message']);
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
    $expectedViewName = 'lessons/toggle-favourited.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must select a valid lesson to favourite.', $responseVars['message']);
  }

  public function testToggleToNotFavourited() {
    $lessonId = 100;
    $personId = 2;
    $lesson = [ 'lesson_id' => $lessonId ];
    $lessonFavourited = [
      'person_id' => $personId,
      'lesson_id' => $lessonId,
      'favourited_at' => '2016-11-01'
    ];
    $this->request->post['lessonId'] = $lessonId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->tutorialRepository->shouldReceive('getLessonById')
      ->once()
      ->with($this->request->post['lessonId'])
      ->andReturn($lesson);
    $this->tutorialRepository->shouldReceive('getLessonFavourited')
      ->once()
      ->with($personId, $this->request->post['lessonId'])
      ->andReturn($lessonFavourited);
    $this->tutorialRepository->shouldReceive('deleteLessonFavourited')
      ->once()
      ->with($personId, $this->request->post['lessonId']);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/toggle-favourited.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('info', $responseVars['status']);
    $this->assertSame('Lesson removed from favourites.', $responseVars['message']);
  }

  public function testToggleToFavourited() {
    $lessonId = 100;
    $personId = 2;
    $lesson = [ 'lesson_id' => $lessonId ];
    $this->request->post['lessonId'] = $lessonId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->tutorialRepository
      ->shouldReceive('getLessonById')
      ->once()
      ->with($this->request->post['lessonId'])
      ->andReturn($lesson);
    $this->tutorialRepository
      ->shouldReceive('getLessonFavourited')
      ->once()
      ->with($personId, $this->request->post['lessonId'])
      ->andReturn(NULL);
    $this->tutorialRepository
      ->shouldReceive('insertLessonFavourited')
      ->once()
      ->with($personId, $this->request->post['lessonId']);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/toggle-favourited.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('success', $responseVars['status']);
    $this->assertSame('Lesson added to favourites.', $responseVars['message']);
  }
}
?>
