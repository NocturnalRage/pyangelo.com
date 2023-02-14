<?php
namespace Tests\src\PyAngelo\Controllers\Reference;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Reference\AssetLibraryController;

class AssetLibraryControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->controller = new AssetLibraryController (
      $this->request,
      $this->response,
      $this->auth
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Reference\AssetLibraryController');
  }

  public function testAssetLibraryController() {
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->response = $this->controller->exec();
    $responseVars = $this->response->getVars();
    $expectedViewName = 'reference/asset-library.html.php';
    $expectedPageTitle = 'Asset Library | PyAngelo';
    $expectedMetaDescription = 'PyAngelo has a asset library of sample images, music, and sounds that can be used without needing to upload your own.';
    $this->assertSame($expectedViewName, $this->response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
