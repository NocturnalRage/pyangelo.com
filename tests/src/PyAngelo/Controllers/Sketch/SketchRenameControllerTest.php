<?php
namespace tests\src\PyAngelo\Controllers\Sketch;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Sketch\SketchRenameController;

class SketchRenameControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->sketchRepository = Mockery::mock('PyAngelo\Repositories\SketchRepository');
    $this->controller = new SketchRenameController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Sketch\SketchRenameController');
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/rename.json.php';
    $expectedStatus = 'info';
    $expectedMessage = 'Log in to rename a sketch.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/rename.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must rename your sketch from the PyAngelo website.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoSketchId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/rename.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must select a sketch to rename.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoTitle() {
    $sketchId = 101;
    $this->request->post = ['sketchId' => $sketchId];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/rename.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must give your sketch a title.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testSketchNotInDatabase() {
    $sketchId = 101;
    $this->request->post = [
      'sketchId' => $sketchId,
      'newTitle' => 'great-sketch'
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/rename.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must select a valid sketch to rename.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testSketchNotOwner() {
    $ownerId = 101;
    $personId = 102;
    $sketchId = 10;
    $sketch = ['sketch_id' => $sketchId, 'person_id' => $ownerId];
    $this->request->post = [
      'sketchId' => $sketchId,
      'newTitle' => 'great-sketch'
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/rename.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must be the owner of the sketch to rename it.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testRenameSuccess() {
    $ownerId = 101;
    $personId = $ownerId;
    $sketchId = 220;
    $fileId = 1000;
    $newTitle = 'new-title';
    $sketch = [
        'sketch_id' => $sketchId,
        'person_id' => $personId,
        'title' => 'random-title'
    ];
    $this->request->post = [
      'sketchId' => $sketchId,
      'newTitle' => $newTitle
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);
    $this->sketchRepository->shouldReceive('renameSketch')->once()->with($sketchId, $newTitle)->andReturn(1);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/rename.json.php';
    $expectedStatus = 'success';
    $expectedMessage = 'File renamed.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }
}
?>
