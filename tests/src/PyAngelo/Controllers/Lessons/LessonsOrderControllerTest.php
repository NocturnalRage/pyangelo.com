<?php
namespace Tests\src\PyAngelo\Controllers\Lessons;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Lessons\LessonsOrderController;

class LessonsOrderControllerTest extends TestCase {
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
    $this->controller = new LessonsOrderController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Lessons\LessonsOrderController');
  }

  public function testLessonsOrderWhenNotAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/order.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You are not authorised!';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testLessonOrderWhenAdminWithNoTutorial() {
    $this->request->post = [];
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/order.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'The tutorial was not specified!';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testLessonOrderWhenAdminWithInvalidTutorial() {
    $tutorialSlug = 'invalid-tutorial';
    $this->request->post['slug'] = $tutorialSlug;
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->tutorialRepository
      ->shouldReceive('getTutorialBySlug')
      ->once()
      ->with($tutorialSlug)
      ->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/order.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'The tutorial specified does not exist!';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testLessonOrderWhenAdminWithNoOrder() {
    $tutorialId = 10;
    $tutorialSlug = 'a-great-tutorial';
    $tutorial = [
      'tutorial_id' => $tutorialId,
      'slug' => $tutorialSlug
    ];
    $this->request->post['slug'] = $tutorialSlug;
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->tutorialRepository
      ->shouldReceive('getTutorialBySlug')
      ->once()
      ->with($tutorialSlug)
      ->andReturn($tutorial);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/order.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'The order of the lessons was not received!';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testLessonsOrderWhenAdminWithValidData() {
    $tutorialId = 10;
    $tutorialSlug = 'a-great-tutorial';
    $tutorial = [
      'tutorial_id' => $tutorialId,
      'slug' => $tutorialSlug
    ];
    $this->request->post['slug'] = $tutorialSlug;
    $this->request->post['idsInOrder'] = ['lesson-1', 'lesson-2'];
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->tutorialRepository
      ->shouldReceive('getTutorialBySlug')
      ->once()
      ->with($tutorialSlug)
      ->andReturn($tutorial);
    $this->tutorialRepository
      ->shouldReceive('updateLessonOrder')
      ->once()
      ->with($tutorialId, 'lesson-1', 1)
      ->andReturn(1);
    $this->tutorialRepository
      ->shouldReceive('updateLessonOrder')
      ->once()
      ->with($tutorialId, 'lesson-2', 2)
      ->andReturn(1);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/order.json.php';
    $expectedStatus = 'success';
    $expectedMessage = 'The new order has been saved.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }
}
?>
