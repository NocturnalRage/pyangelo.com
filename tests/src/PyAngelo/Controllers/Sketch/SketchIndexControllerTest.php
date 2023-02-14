<?php
namespace Tests\src\PyAngelo\Controllers\Sketch;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Repositories\SketchRepository;
use PyAngelo\Controllers\Sketch\SketchIndexController;

class SketchIndexControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $sketchRepository;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->sketchRepository = Mockery::mock('PyAngelo\Repositories\SketchRepository');
    $this->controller = new SketchIndexController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Sketch\SketchIndexController');
  }

  public function testRedirectToLoginPageWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('You must be logged in to view your sketches', $_SESSION['flash']['message']);
  }

  public function testSuccessShowView() {
    $sketch1 = [
        'sketch_id' => 1,
        'deleted' => 0
    ];
    $sketch2 = [
        'sketch_id' => 2,
        'deleted' => 1
    ];
    $sketches = [
      $sketch1,
      $sketch2
    ];
    $personId = 101;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->sketchRepository->shouldReceive('getSketches')->once()->with($personId)->andReturn($sketches);
    $this->sketchRepository->shouldReceive('getCollections')->once()->with($personId)->andReturn();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/index.html.php';
    $expectedPageTitle = 'My PyAngelo Sketches';
    $expectedMetaDescription = "View all the great sketches you have been created on PyAngelo.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
