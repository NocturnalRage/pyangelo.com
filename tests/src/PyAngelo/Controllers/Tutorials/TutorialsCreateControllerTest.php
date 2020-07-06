<?php
namespace tests\src\PyAngelo\Controllers\Tutorials;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Tutorials\TutorialsCreateController;

class TutorialsCreateControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialFormService = Mockery::mock('PyAngelo\FormServices\TutorialFormService');
    $this->controller = new TutorialsCreateController (
      $this->request,
      $this->response,
      $this->auth,
      $this->tutorialFormService
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Tutorials\TutorialsCreateController');
  }

  public function testWhenNotAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenAdminWithNoData() {
    session_start();
    $this->request->post = [];
    $this->request->files['thumbnail'] = [];
    $this->request->files['pdf'] = [];
    $errors = [ 'foo' => 'bar' ];
    $flashMessage = 'Flash Gordon';
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->tutorialFormService->shouldReceive('createTutorial')
      ->once()
      ->with($this->request->post, $this->request->files['thumbnail'], $this->request->files['pdf'])
      ->andReturn(false);
    $this->tutorialFormService->shouldReceive('getErrors')
      ->once()
      ->with()
      ->andReturn($errors);
    $this->tutorialFormService->shouldReceive('getFlashMessage')
      ->once()
      ->with()
      ->andReturn($flashMessage);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /tutorials/new'));
    $expectedErrors = $errors;
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($flashMessage, $this->request->session['flash']['message']);
  }

  public function testWhenAdminWithValidData() {
    $this->request->post = [
      'title' => 'Tutorial 1'
    ];
    $this->request->files['thumbnail'] = [];
    $this->request->files['pdf'] = [];
    $errors = [ 'foo' => 'bar' ];
    $flashMessage = 'Flash Gordon';
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->tutorialFormService->shouldReceive('createTutorial')
      ->once()
      ->with($this->request->post, $this->request->files['thumbnail'], $this->request->files['pdf'])
      ->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /tutorials'));
    $expectedErrors = $errors;
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
