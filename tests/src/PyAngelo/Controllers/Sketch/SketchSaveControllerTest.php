<?php
namespace tests\src\PyAngelo\Controllers\Sketch;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Repositories\SketchRepository;
use PyAngelo\Utilities\SketchFiles;
use PyAngelo\Controllers\Sketch\SketchSaveController;

class SketchSaveControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->sketchRepository = Mockery::mock('PyAngelo\Repositories\SketchRepository');
    $this->sketchFiles = Mockery::mock('PyAngelo\Utilities\SketchFiles');
    $this->controller = new SketchSaveController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Sketch\SketchSaveController');
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/saved.json.php';
    $expectedStatus = 'info';
    $expectedMessage = 'Log in to save a sketch.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/saved.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must update your sketch from the PyAngelo website.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoSlug() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/saved.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must select a sketch to update.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoFilename() {
    $sketchId = 101;
    $this->request->post = ['sketchId' => $sketchId];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/saved.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must select a filename to save.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoProgram() {
    $sketchId = 101;
    $filename = 'main.py';
    $this->request->post = [
      'sketchId' => $sketchId,
      'filename' => $filename
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/saved.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must have code to save.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testSketchNotInDatabase() {
    $sketchId = 101;
    $filename = 'main.py';
    $program = 'canvas.background()';
    $this->request->post = [
      'sketchId' => $sketchId,
      'filename' => $filename,
      'program' => $program
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/saved.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must select a valid sketch to update.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testSketchNotOwner() {
    $ownerId = 101;
    $personId = 102;
    $sketchId = 10;
    $filename = 'main.py';
    $sketch = ['sketch_id' => $sketchId, 'person_id' => $ownerId];
    $program = 'canvas.background()';
    $this->request->post = [
      'sketchId' => $sketchId,
      'filename' => $filename,
      'program' => $program
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/saved.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must be the owner of the sketch to update it.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testSuccess() {
    $ownerId = 101;
    $personId = $ownerId;
    $sketchId = 220;
    $fileId = 1000;
    $filename = 'main.py';
    $sketch = [
        'sketch_id' => $sketchId,
        'person_id' => $personId,
        'title' => 'random-title'
    ];
    $program = 'canvas.background()';
    $this->request->post = [
      'sketchId' => $sketchId,
      'filename' => $filename,
      'program' => $program
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);
    $this->sketchFiles->shouldReceive('saveCode')->once()->with($sketch, $filename, $program)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/saved.json.php';
    $expectedStatus = 'success';
    $expectedMessage = 'File saved.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }
}
?>
