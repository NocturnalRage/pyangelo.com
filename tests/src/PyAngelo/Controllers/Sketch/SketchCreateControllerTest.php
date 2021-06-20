<?php
namespace Tests\src\PyAngelo\Controllers\Sketch;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Repositories\SketchRepository;
use PyAngelo\Utilities\SketchFiles;
use PyAngelo\Controllers\Sketch\SketchCreateController;

class SketchCreateControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->sketchRepository = Mockery::mock('PyAngelo\Repositories\SketchRepository');
    $this->sketchFiles = Mockery::mock('PyAngelo\Utilities\sketchFiles');
    $this->controller = new SketchCreateController (
      $this->request,
      $this->response,
      $this->auth,
      $this->sketchRepository,
      $this->sketchFiles
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Sketch\SketchCreateController');
  }

  public function testRedirectToLoginPageWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller ->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('You must be logged in to create a new sketch', $this->request->session['flash']['message']);
  }

  public function testInvalidPostData() {
    $personId = 101;
    $title = 'funny-name';
    $errors = ['foo' => 'bar' ];
    $flashMessage = 'Error! We could not create a new sketch for you :(';
    $this->request->post = ['data' => 'invalid'];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('createNewSketch')->once()->with($personId, \Mockery::any())->andReturn(false);

    $response = $this->controller ->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($flashMessage, $this->request->session['flash']['message']);
  }

  public function testSuccess() {
    $personId = 101;
    $sketchId = 10;
    $sketch = ['sketch_id' => $sketchId];
    $this->request->post = ['data' => 'invalid'];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('createNewSketch')->once()->with($personId, \Mockery::any())->andReturn($sketchId);
    $this->sketchFiles->shouldReceive('createNewMain')->once()->with($personId, $sketchId)->andReturn();

    $response = $this->controller ->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch/' . $sketch['sketch_id']));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
