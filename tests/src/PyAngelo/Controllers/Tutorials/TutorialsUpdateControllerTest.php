<?php
namespace Tests\src\PyAngelo\Controllers\Tutorials;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Tutorials\TutorialsUpdateController;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class TutorialsUpdateControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $tutorialFormService;
  protected $controller;


  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialFormService = Mockery::mock('PyAngelo\FormServices\TutorialFormService');
    $this->controller = new TutorialsUpdateController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Tutorials\TutorialsUpdateController');
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

  public function testWhenAdminWithNoSlug() {
    $this->request->post = [];
    $this->request->files['thumbnail'] = [];
    $this->request->files['pdf'] = [];
    $flashMessage = 'Flash Gordon';
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  #[RunInSeparateProcess]
  public function testWhenAdminWithNoData() {
    session_start();
    $this->request->post = ['slug' => 'tutorial-1'];
    $this->request->files['thumbnail'] = [];
    $this->request->files['pdf'] = [];
    $errors = [ 'foo' => 'bar' ];
    $flashMessage = 'Flash Gordon';
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->tutorialFormService
      ->shouldReceive('updateTutorial')
      ->once()
      ->with($this->request->post, $this->request->files['thumbnail'], $this->request->files['pdf'])
      ->andReturn(false);
    $this->tutorialFormService
      ->shouldReceive('getErrors')
      ->once()
      ->with()
      ->andReturn($errors);
    $this->tutorialFormService
      ->shouldReceive('getFlashMessage')
      ->once()
      ->with()
      ->andReturn($flashMessage);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /tutorials/tutorial-1/edit'));
    $expectedErrors = $errors;
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testWhenAdminWithValidData() {
    $this->request->post = [
      'title' => 'Tutorial 1',
      'slug' => 'tutorial-1'
    ];
    $this->request->files['thumbnail'] = [];
    $this->request->files['pdf'] = [];
    $errors = [ 'foo' => 'bar' ];
    $flashMessage = 'Flash Gordon';
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->tutorialFormService->shouldReceive('updateTutorial')
      ->once()
      ->with($this->request->post, $this->request->files['thumbnail'], $this->request->files['pdf'])
      ->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /tutorials/tutorial-1'));
    $expectedErrors = $errors;
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
