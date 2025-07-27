<?php
namespace Tests\src\PyAngelo\Controllers\Lessons;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Lessons\LessonsCreateController;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class LessonsCreateControllerTest extends TestCase {
  protected $lessonFormService;
  protected $request;
  protected $response;
  protected $auth;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->lessonFormService = Mockery::mock('PyAngelo\FormServices\LessonFormService');
    $this->controller = new LessonsCreateController (
      $this->request,
      $this->response,
      $this->auth,
      $this->lessonFormService
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Lessons\LessonsCreateController');
  }

  public function testLessonsCreateWhenNotAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  #[RunInSeparateProcess]
  public function testWhenAdminWithNoDataUpdate() {
    session_start();
    $slug = 'a-tutorial';
    $this->request->post = [ 'slug' => $slug ];
    $this->request->files['poster'] = [];
    $errors = [ 'foo' => 'bar' ];
    $flashMessage = 'Flash Gordon';
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->lessonFormService->shouldReceive('createLesson')
      ->once()
      ->with($this->request->post, $this->request->files['poster'])
      ->andReturn(false);
    $this->lessonFormService->shouldReceive('getErrors')
      ->once()
      ->with()
      ->andReturn($errors);
    $this->lessonFormService->shouldReceive('getFlashMessage')
      ->once()
      ->with()
      ->andReturn($flashMessage);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /tutorials/' . $slug . '/lessons/new';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testWhenAdminWithValidDataUpdate() {
    $slug = 'a-tutorial';
    $this->request->post = [
      'slug' => $slug,
      'lesson_title' => 'Lesson 1'
    ];
    $this->request->files['poster'] = [];
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->lessonFormService->shouldReceive('createLesson')
      ->once()
      ->with($this->request->post, $this->request->files['poster'])
      ->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /tutorials/' . $slug;
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
