<?php
namespace Tests\src\PyAngelo\Controllers\Collections;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Repositories\SketchRepository;
use PyAngelo\Controllers\Collections\CollectionsCreateController;

class CollectionsCreateControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->sketchRepository = Mockery::mock('PyAngelo\Repositories\SketchRepository');
    $this->controller = new CollectionsCreateController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Collections\CollectionsCreateController');
  }

  public function testRedirectToLoginPageWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller ->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('You must be logged in to create a new sketch', $_SESSION['flash']['message']);
  }

  public function testWhenNoCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller ->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('Please create collections from the PyAngelo website!', $_SESSION['flash']['message']);
  }

  public function testNoCollectionId() {
    $personId = 101;
    $flashMessage = 'You must provide a name for your collection.';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller ->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($flashMessage, $_SESSION['flash']['message']);
  }

  public function testCollectionIdNotReturned() {
    $personId = 101;
    $collectionTitle = "New Collection";
    $this->request->post['collectionTitle'] = $collectionTitle;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('createNewCollection')->once()->with($personId, $collectionTitle)->andReturn();

    $response = $this->controller ->exec();
    $responseVars = $response->getVars();
    $expectedFlashMessage = 'Error! We could not create a new collection for you :(';
    $expectedHeaders = array(array('header', 'Location: /sketch'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testSuccessCreateCollection() {
    $personId = 101;
    $collectionTitle = "New Collection";
    $collectionId = 10;
    $this->request->post = ['collectionTitle' => $collectionTitle];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('createNewCollection')->once()->with($personId, $collectionTitle)->andReturn($collectionId);

    $response = $this->controller ->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
