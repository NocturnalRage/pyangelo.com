<?php
namespace Tests\src\PyAngelo\Controllers\Sketch;

use PHPUnit\Framework\TestCase;
use Mockery;
use Dotenv\Dotenv;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Sketch\SketchEditorController;

class SketchEditorControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->controller = new SketchEditorController (
      $this->request,
      $this->response,
      $this->auth
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Sketch\SketchEditorController');
  }

  public function testEditor() {
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/editor.html.php';
    $expectedPageTitle = 'PyAngelo Editor';
    $expectedMetaDescription = "The PyAngelo editor lets you code without needing an account. You can experiment by coding on this page but you cannot save your work. For this you need to create an account. You also are not able to upload sound files or images via this editor so we encourage you to create your free account.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
