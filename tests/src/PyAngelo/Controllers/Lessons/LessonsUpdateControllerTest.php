<?php
namespace Tests\src\PyAngelo\Controllers\Lessons;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Lessons\LessonsUpdateController;

class LessonsUpdateControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->lessonFormService = Mockery::mock('PyAngelo\FormServices\LessonFormService');
    $this->controller = new LessonsUpdateController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Lessons\LessonsUpdateController');
  }

  public function testWhenNotAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenAdminWithNoData() {
    session_start();
    $slug = 'a-tutorial';
    $lessonSlug = 'a-lesson';
    $this->request->post = [ 'slug' => $slug, 'lesson_slug' => $lessonSlug ];
    $this->request->files['poster'] = [];
    $errors = [ 'foo' => 'bar' ];
    $flashMessage = 'Flash Gordon';
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->lessonFormService
      ->shouldReceive('updateLesson')
      ->once()
      ->with($this->request->post, $this->request->files['poster'])
      ->andReturn(false);
    $this->lessonFormService
      ->shouldReceive('getErrors')
      ->once()
      ->with()
      ->andReturn($errors);
    $this->lessonFormService
      ->shouldReceive('getFlashMessage')
      ->once()
      ->with()
      ->andReturn($flashMessage);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /tutorials/' . $slug . '/lessons/' . $lessonSlug . '/edit';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenAdminWithValidData() {
    session_start();
    $slug = 'a-tutorial';
    $lessonSlug = 'a-lesson';
    $this->request->post = [
      'slug' => $slug,
      'lesson_slug' => $lessonSlug,
      'lesson_title' => 'Lesson 1'
    ];
    $this->request->files['poster'] = [];
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->lessonFormService
      ->shouldReceive('updateLesson')
      ->once()
      ->with($this->request->post, $this->request->files['poster'])
      ->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /tutorials/' . $slug . '/' . $lessonSlug;
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
