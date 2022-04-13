<?php
namespace Tests\src\PyAngelo\Controllers\Sketch;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Repositories\SketchRepository;
use PyAngelo\Utilities\SketchFiles;
use PyAngelo\Controllers\Sketch\SketchDeleteFileController;

class SketchDeleteFileControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->sketchRepository = Mockery::mock('PyAngelo\Repositories\SketchRepository');
    $this->sketchFiles = Mockery::mock('PyAngelo\Utilities\SketchFiles');
    $this->controller = new SketchDeleteFileController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Sketch\SketchDeleteFileController');
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/delete.json.php';
    $expectedStatus = 'info';
    $expectedMessage = 'Log in to delete a file from a sketch.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/delete.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must delete a file from the PyAngelo website.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoSketchId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/delete.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must select a sketch to delete a file from.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoFilename() {
    $sketchId = bin2hex(random_bytes(16));
    $this->request->post = ['sketchId' => $sketchId];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/delete.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must select a file to delete.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testSketchNotInDatabase() {
    $sketchId = bin2hex(random_bytes(16));
    $filename = 'main.py';
    $this->request->post = [
      'sketchId' => $sketchId,
      'filename' => $filename
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/delete.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must select a valid sketch to delete a file from.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testSketchNotOwner() {
    $ownerId = 101;
    $personId = 102;
    $sketchId = bin2hex(random_bytes(16));
    $filename = 'main.py';
    $sketch = ['sketch_id' => $sketchId, 'person_id' => $ownerId];
    $this->request->post = [
      'sketchId' => $sketchId,
      'filename' => $filename
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/delete.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must be the owner of the sketch to delete a file from it.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }
  
  public function testAttemptDeleteMain() {
    $personId = 101;
    $ownerId = 101;
    $sketchId = bin2hex(random_bytes(16));
    $filename = 'main.py';
    $this->request->post = [
      'sketchId' => $sketchId,
      'filename' => $filename
    ];
    $sketch = ['sketch_id' => $sketchId, 'person_id' => $ownerId];
    $this->request->post = [
      'sketchId' => $sketchId,
      'filename' => $filename
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/delete.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You cannot delete main.py!';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }
  
  public function testFileDoesNotExist() {
    $ownerId = 101;
    $personId = $ownerId;
    $sketchId = bin2hex(random_bytes(16));
    $filename = 'randomFile.py';
    $this->request->post = [
      'sketchId' => $sketchId,
      'filename' => $filename
    ];
    $sketch = ['sketch_id' => $sketchId, 'person_id' => $ownerId];
    $this->request->post = [
      'sketchId' => $sketchId,
      'filename' => $filename
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);
    $this->sketchFiles->shouldReceive('doesFileExist')->once()->with($sketch, $filename)->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/delete.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'The requested file does not exist.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testSuccess() {
    $ownerId = 101;
    $personId = $ownerId;
    $sketchId = bin2hex(random_bytes(16));
    $fileId = 1000;
    $filename = 'randomFile.py';
    $sketch = [
        'sketch_id' => $sketchId,
        'person_id' => $personId,
        'title' => 'random-title'
    ];
    $this->request->post = [
      'sketchId' => $sketchId,
      'filename' => $filename
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);
    $this->sketchRepository->shouldReceive('deleteSketchFile')->once()->with($sketchId, $filename)->andReturn();
    $this->sketchFiles->shouldReceive('doesFileExist')->once()->with($sketch, $filename)->andReturn(true);
    $this->sketchFiles->shouldReceive('deleteFile')->once()->with($sketch, $filename)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/delete.json.php';
    $expectedStatus = 'success';
    $expectedMessage = 'File deleted.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }
}
?>
