<?php
namespace Tests\src\PyAngelo\Controllers\Sketch;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Sketch\SketchDeleteController;

class SketchDeleteControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->sketchRepository = Mockery::mock('PyAngelo\Repositories\SketchRepository');
    $this->controller = new SketchDeleteController (
      $this->request,
      $this->response,
      $this->auth,
      $this->sketchRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Sketch\SketchDeleteController');
  }

  public function testSketchDeleteControllerWhenNoSketchId() {
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch'));
    $expectedFlashMessage = "You must select a sketch to delete";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testSketchDeleteControllerWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $sketchId = 999;
    $this->request->post["sketchId"] = $sketchId;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $expectedFlashMessage = "You must be logged in to delete a sketch!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testSketchDeleteControllerWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);
    $sketchId = 999;
    $this->request->post["sketchId"] = $sketchId;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch'));
    $expectedFlashMessage = "Please delete sketches from the PyAngelo website!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testSketchDeleteControllerWhenNotValidSketch() {
    $sketchId = 999;
    $this->request->post["sketchId"] = $sketchId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn(NULL);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch'));
    $expectedFlashMessage = "You must select a valid sketch to delete!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testSketchDeleteControllerWhenNotOwnerOfSketch() {
    $personId = 88;
    $sketchPersonId = 92;
    $sketchId = 999;
    $this->request->post["sketchId"] = $sketchId;
    $sketch = [
      'sketch' => $sketchId,
      'person_id' => $sketchPersonId
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch'));
    $expectedFlashMessage = "You must be the owner of the sketch to delete it.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testSketchDeleteControllerWhenCannotDelete() {
    $personId = 88;
    $sketchPersonId = 88;
    $sketchId = 999;
    $this->request->post["sketchId"] = $sketchId;
    $sketch = [
      'sketch' => $sketchId,
      'person_id' => $sketchPersonId
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);
    $this->sketchRepository->shouldReceive('deleteSketch')->once()->with($sketchId)->andReturn(0);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch'));
    $expectedFlashMessage = "Sorry, we could not delete the sketch.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testSketchDeleteControllerSuccess() {
    $personId = 88;
    $sketchPersonId = 88;
    $sketchId = 999;
    $this->request->post["sketchId"] = $sketchId;
    $sketch = [
      'sketch' => $sketchId,
      'person_id' => $sketchPersonId
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);
    $this->sketchRepository->shouldReceive('deleteSketch')->once()->with($sketchId)->andReturn(1);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch'));
    $expectedFlashMessage = "Your sketch has been deleted.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }
}
?>
