<?php
namespace Tests\src\PyAngelo\Controllers\Collections;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Collections\CollectionsRenameController;

class CollectionsRenameControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->sketchRepository = Mockery::mock('PyAngelo\Repositories\SketchRepository');
    $this->controller = new CollectionsRenameController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Collections\CollectionsRenameController');
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'collections/rename.json.php';
    $expectedStatus = 'info';
    $expectedMessage = 'Log in to rename a collection.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'collections/rename.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must rename your collection from the PyAngelo website.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoCollectionId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'collections/rename.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must select a collection to rename.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoTitle() {
    $collectionId = 101;
    $this->request->post = ['collectionId' => $collectionId];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'collections/rename.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must give your collection a title.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenTitleOnlyWhitespace() {
    $collectionId = 101;
    $this->request->post = [
      'collectionId' => $collectionId,
      'newTitle' => '   '
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'collections/rename.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must give your collection a title.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testCollectionNotInDatabase() {
    $collectionId = 101;
    $this->request->post = [
      'collectionId' => $collectionId,
      'newTitle' => 'great-sketch'
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->sketchRepository->shouldReceive('getCollectionById')->once()->with($collectionId)->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'collections/rename.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must select a valid collection to rename.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testCollectionNotOwner() {
    $ownerId = 101;
    $personId = 102;
    $collectionId = 10;
    $collection = ['collection_id' => $collectionId, 'person_id' => $ownerId];
    $this->request->post = [
      'collectionId' => $collectionId,
      'newTitle' => 'Great Collection'
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getCollectionById')->once()->with($collectionId)->andReturn($collection);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'collections/rename.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must be the owner of the collection to rename it.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testRenameSuccess() {
    $ownerId = 101;
    $personId = $ownerId;
    $collectionId = 220;
    $fileId = 1000;
    $newTitle = 'new-title';
    $collection = [
        'collection_id' => $collectionId,
        'person_id' => $personId,
        'title' => 'random-title'
    ];
    $this->request->post = [
      'collectionId' => $collectionId,
      'newTitle' => $newTitle
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getCollectionById')->once()->with($collectionId)->andReturn($collection);
    $this->sketchRepository->shouldReceive('renameCollection')->once()->with($collectionId, $newTitle)->andReturn(1);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'collections/rename.json.php';
    $expectedStatus = 'success';
    $expectedMessage = 'Collection renamed.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }
}
?>
