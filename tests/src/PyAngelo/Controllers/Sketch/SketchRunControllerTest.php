<?php
namespace Tests\src\PyAngelo\Controllers\Sketch;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Repositories\SketchRepository;
use PyAngelo\Controllers\Sketch\SketchRunController;

class SketchRunControllerTest extends TestCase {
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
    $this->controller = new SketchRunController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Sketch\SketchRunController');
  }

  public function testWhenNoSlug() {
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testWhenSlugNotInDatabase() {
    $sketchId = 101;
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn(NULL);
    $this->request->get['sketchId'] = $sketchId;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testWhenValidSlug() {
    $sketchId = 101;
    $sketch = [
      'sketch_id' => $sketchId,
      'title' => 'My Great Sketch'
    ];
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);
    $this->request->get['sketchId'] = $sketchId;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/run.html.php';
    $expectedPageTitle = $sketch['title'] . ' | PyAngelo';
    $expectedMetaDescription = 'Another Wonderful PyAngelo Sketch';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
