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

  public function testWhenNoCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller ->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('Please create sketches from the PyAngelo website!', $this->request->session['flash']['message']);
  }

  public function testNoSketchIdReturned() {
    $personId = 101;
    $title = 'funny-name';
    $errors = ['foo' => 'bar' ];
    $flashMessage = 'Error! We could not create a new sketch for you :(';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('createNewSketch')->once()->with($personId, \Mockery::any(), null)->andReturn();

    $response = $this->controller ->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($flashMessage, $this->request->session['flash']['message']);
  }

  public function testSuccessWithNoCollectionId() {
    $personId = 101;
    $sketchId = 10;
    $sketch = ['sketch_id' => $sketchId];
    $this->request->post = ['data' => 'invalid'];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('createNewSketch')->once()->with($personId, \Mockery::any(), null)->andReturn($sketchId);
    $this->sketchFiles->shouldReceive('createNewMain')->once()->with($personId, $sketchId)->andReturn();

    $response = $this->controller ->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch/' . $sketch['sketch_id']));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testSuccessWithInvalidCollectionId() {
    $collectionId = 5;
    $personId = 101;
    $sketchId = 10;
    $sketch = ['sketch_id' => $sketchId];
    $this->request->post = ['collectionId' => $collectionId];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('createNewSketch')->once()->with($personId, \Mockery::any(), null)->andReturn($sketchId);
    $this->sketchRepository->shouldReceive('getCollectionById')->once()->with($collectionId)->andReturn();
    $this->sketchFiles->shouldReceive('createNewMain')->once()->with($personId, $sketchId)->andReturn();

    $response = $this->controller ->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch/' . $sketch['sketch_id']));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testSuccessWhenNotOwnerOfCollection() {
    $collectionId = 5;
    $ownerId = 10;
    $personId = 11;
    $collection = [
      'collection_id' => $collectionId,
      'person_id' => $ownerId
    ];
    $sketchId = 10;
    $sketch = ['sketch_id' => $sketchId];
    $this->request->post = ['collectionId' => $collectionId];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(3)->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('createNewSketch')->once()->with($personId, \Mockery::any(), null)->andReturn($sketchId);
    $this->sketchRepository->shouldReceive('getCollectionById')->once()->with($collectionId)->andReturn($collection);
    $this->sketchFiles->shouldReceive('createNewMain')->once()->with($personId, $sketchId)->andReturn();

    $response = $this->controller ->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch/' . $sketch['sketch_id']));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testSuccessWhenOwnerOfCollection() {
    $collectionId = 5;
    $ownerId = 11;
    $personId = 11;
    $collection = [
      'collection_id' => $collectionId,
      'person_id' => $ownerId
    ];
    $sketchId = 10;
    $sketch = ['sketch_id' => $sketchId];
    $this->request->post = ['collectionId' => $collectionId];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(3)->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('createNewSketch')->once()->with($personId, \Mockery::any(), $collectionId)->andReturn($sketchId);
    $this->sketchRepository->shouldReceive('getCollectionById')->once()->with($collectionId)->andReturn($collection);
    $this->sketchFiles->shouldReceive('createNewMain')->once()->with($personId, $sketchId)->andReturn();

    $response = $this->controller ->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch/' . $sketch['sketch_id']));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
