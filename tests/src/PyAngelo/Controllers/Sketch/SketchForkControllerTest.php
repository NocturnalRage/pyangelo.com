<?php
namespace Tests\src\PyAngelo\Controllers\Sketch;

use PHPUnit\Framework\TestCase;
use Mockery;
use Dotenv\Dotenv;
use Framework\Request;
use Framework\Response;
use PyAngelo\Repositories\SketchRepository;
use PyAngelo\Controllers\Sketch\SketchForkController;

class SketchForkControllerTest extends TestCase {
  protected $appDir;
  protected $request;
  protected $response;
  protected $auth;
  protected $sketchRepository;
  protected $sketchFiles;
  protected $controller;

  public function setUp(): void {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../../../', '.env.test');
    $dotenv->load();
    $this->appDir = $_ENV['APPLICATION_DIRECTORY'];;
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->sketchRepository = Mockery::mock('PyAngelo\Repositories\SketchRepository');
    $this->sketchFiles = Mockery::mock('PyAngelo\Utilities\SketchFiles');
    $this->controller = new SketchForkController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Sketch\SketchForkController');
  }

  public function testRedirectsToLoginPageWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testRedirectsToHomePageWhenCrsfTokenInvalid() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testRedirectsToHomePageWhenNoSketchId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testRedirectsToHomePageWhenSketchNotInDatabase() {
    $anySketchId = bin2hex(random_bytes(16));
    $this->request->post['sketchId'] = $anySketchId;

    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $anyPersonId = 101;
    $this->sketchRepository
         ->shouldReceive('getSketchById')
         ->once()
         ->with($anySketchId)
         ->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testRedirectsToSketchPageWhenForkFails() {
    $anySketchId = 101;
    $anyPersonId = 101;
    $anySketchTitle = 'My Sketch';
    $anyLayout = 'cols';
    $anySketch = [
      'sketch_id' => $anySketchId,
      'person_id' => $anyPersonId,
      'title' => $anySketchTitle,
      'layout' => $anyLayout
    ];
    $this->request->post['sketchId'] = $anySketchId;

    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $anyPersonId = 101;
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($anyPersonId);
    $this->sketchRepository
         ->shouldReceive('getSketchById')
         ->once()
         ->with($anySketchId)
         ->andReturn($anySketch);
    $this->sketchRepository
         ->shouldReceive('forkSketch')
         ->once()
         ->with($anySketchId, $anyPersonId, $anySketchTitle, NULL, NULL, $anyLayout)
         ->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch/' . $anySketchId));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testSketchForkedSuccessfully() {
    $anySketchId = 101;
    $anyPersonId = 101;
    $anySketchTitle = 'My Sketch';
    $anyLayout = 'cols';
    $anySketch = [
      'sketch_id' => $anySketchId,
      'person_id' => $anyPersonId,
      'title' => $anySketchTitle,
      'layout' => $anyLayout
    ];
    $newSketchId = 1000;
    $newPersonId = 1001;
    $newSketch = [
      'sketch_id' => $newSketchId,
      'person_id' => $newPersonId,
      'title' => $anySketchTitle
    ];
    $sketchFiles = [
      'name' => 'main.py'
    ];
    $this->request->post['sketchId'] = $anySketchId;

    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($newPersonId);
    $this->sketchRepository
         ->shouldReceive('getSketchById')
         ->once()
         ->with($anySketchId)
         ->andReturn($anySketch);
    $this->sketchRepository
         ->shouldReceive('forkSketch')
         ->once()
         ->with($anySketchId, $newPersonId, $anySketchTitle, NULL, NULL, $anyLayout)
         ->andReturn($newSketchId);
    $this->sketchRepository
         ->shouldReceive('getSketchFiles')
         ->once()
         ->with($newSketchId)
         ->andReturn($sketchFiles);

    $this->sketchFiles
         ->shouldReceive('forkSketch')
         ->once()
         ->with($anySketch, $newPersonId, $newSketchId, $sketchFiles);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /sketch/' . $newSketchId));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
