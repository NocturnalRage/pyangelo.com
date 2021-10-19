<?php
namespace Tests\src\PyAngelo\Controllers\Collections;

use PHPUnit\Framework\TestCase;
use Mockery;
use Dotenv\Dotenv;
use Framework\Request;
use Framework\Response;
use PyAngelo\Repositories\SketchRepository;
use PyAngelo\Controllers\Collections\CollectionsShowController;

class CollectionsShowControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->sketchRepository = Mockery::mock('PyAngelo\Repositories\SketchRepository');
    $this->controller = new CollectionsShowController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Collections\CollectionsShowController');
  }

  public function testRedirectToPageNotFoundWhenNoCollectionId() {
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testRedirectToPageNotFoundWhenCollectionNotInDatabase() {
    $collectionId = 101;
    $this->request->get['collectionId'] = $collectionId;
    $this->sketchRepository->shouldReceive('getCollectionById')->once()->with($collectionId)->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testWhenNotOwnerOfCollection() {
    $collectionId = 100;
    $ownerId = 1000;
    $personId = 999;

    $collection = [
      'collection_id' => $collectionId,
      'person_id' => $ownerId,
      'collection_name' => 'My Great Collection'
    ];
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getCollectionById')->once()->with($collectionId)->andReturn($collection);
    $this->request->get['collectionId'] = $collectionId;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You can only view your own collections!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testWhenOwnerOfCollection() {
    $collectionId = 100;
    $ownerId = 1000;
    $personId = 1000;

    $collection = [
      'collection_id' => $collectionId,
      'person_id' => $ownerId,
      'collection_name' => 'My Great Collection'
    ];
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->sketchRepository->shouldReceive('getCollectionById')->once()->with($collectionId)->andReturn($collection);
    $this->sketchRepository->shouldReceive('getCollections')->once()->with($personId)->andReturn();
    $this->sketchRepository->shouldReceive('getCollectionSketches')->once()->with($collectionId)->andReturn();
    $this->request->get['collectionId'] = $collectionId;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/index.html.php';
    $expectedPageTitle = $collection['collection_name'] . ' | PyAngelo';
    $expectedMetaDescription = "Your PyAngelo sketches stored in a collection named: " . $collection['collection_name'];
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
