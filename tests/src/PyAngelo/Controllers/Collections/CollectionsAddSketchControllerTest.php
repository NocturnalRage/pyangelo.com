<?php
namespace Tests\src\PyAngelo\Controllers\Collections;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Repositories\SketchRepository;
use PyAngelo\Controllers\Collections\CollectionsAddSketchController;

class CollectionsAddSketchControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->sketchRepository = Mockery::mock('PyAngelo\Repositories\SketchRepository');
    $this->controller = new CollectionsAddSketchController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Collections\CollectionsAddSketchController');
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'collections/add-sketch.json.php';
    $expectedStatus = 'info';
    $expectedMessage = 'Log in to add a sketch to a collection.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'collections/add-sketch.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must add a sketch to a collection from the PyAngelo website.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoSketchId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'collections/add-sketch.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must select a sketch to add to your collection.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenInvalidSketchId() {
    $sketchId = 'invalid';
    $this->request->post['sketchId'] = $sketchId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'collections/add-sketch.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must select a valid sketch to add to your collection.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNotOwnerOfSketch() {
    $sketchId = 100;
    $ownerId = 20;
    $personId = 10;
    $sketch = [
      'sketch_id' => $sketchId,
      'person_id' => $ownerId
    ];
    $this->request->post['sketchId'] = $sketchId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'collections/add-sketch.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must be the owner of the sketch to add it to your collection.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoCollectionId() {
    $sketchId = 100;
    $ownerId = 10;
    $personId = 10;
    $sketch = [
      'sketch_id' => $sketchId,
      'person_id' => $ownerId
    ];
    $this->request->post['sketchId'] = $sketchId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'collections/add-sketch.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must select a collection for your sketch.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenCollectionIdZeroAndFail() {
    $collectionId = 0;
    $sketchId = 100;
    $ownerId = 10;
    $personId = 10;
    $sketch = [
      'sketch_id' => $sketchId,
      'person_id' => $ownerId
    ];
    $this->request->post = [
      'sketchId' => $sketchId,
      'collectionId' => $collectionId
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);
    $this->sketchRepository->shouldReceive('removeSketchFromAllCollections')->once()->with($sketchId)->andReturn(0);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'collections/add-sketch.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'Sorry, we could not remove your sketch from a collection.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenCollectionIdZeroAndSuccess() {
    $collectionId = 0;
    $sketchId = 100;
    $ownerId = 10;
    $personId = 10;
    $sketch = [
      'sketch_id' => $sketchId,
      'person_id' => $ownerId
    ];
    $this->request->post = [
      'sketchId' => $sketchId,
      'collectionId' => $collectionId
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);
    $this->sketchRepository->shouldReceive('removeSketchFromAllCollections')->once()->with($sketchId)->andReturn(1);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'collections/add-sketch.json.php';
    $expectedStatus = 'removed';
    $expectedMessage = 'Sketch removed from collection';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenCollectionIdNotZeroAndFail() {
    $collectionId = 1111;
    $sketchId = 100;
    $ownerId = 10;
    $personId = 10;
    $sketch = [
      'sketch_id' => $sketchId,
      'person_id' => $ownerId
    ];
    $this->request->post = [
      'sketchId' => $sketchId,
      'collectionId' => $collectionId
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);
    $this->sketchRepository->shouldReceive('addSketchToCollection')->once()->with($sketchId, $collectionId)->andReturn(0);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'collections/add-sketch.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'Sorry, we could not add your sketcch to your collection.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenCollectionIdNotZeroAndSuccess() {
    $collectionId = 1111;
    $sketchId = 100;
    $ownerId = 10;
    $personId = 10;
    $sketch = [
      'sketch_id' => $sketchId,
      'person_id' => $ownerId
    ];
    $this->request->post = [
      'sketchId' => $sketchId,
      'collectionId' => $collectionId
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);
    $this->sketchRepository->shouldReceive('addSketchToCollection')->once()->with($sketchId, $collectionId)->andReturn(1);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'collections/add-sketch.json.php';
    $expectedStatus = 'added';
    $expectedMessage = 'Sketch added to collection';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }
}
?>
