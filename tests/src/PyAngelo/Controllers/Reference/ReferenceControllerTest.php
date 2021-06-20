<?php
namespace Tests\src\PyAngelo\Controllers\Reference;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Reference\ReferenceController;

class ReferenceControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->controller = new ReferenceController (
      $this->request,
      $this->response,
      $this->auth
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Reference\ReferenceController');
  }

  public function testReferenceController() {
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->response = $this->controller->exec();
    $responseVars = $this->response->getVars();
    $expectedViewName = 'reference/reference.html.php';
    $expectedPageTitle = 'Reference | PyAngelo';
    $expectedMetaDescription = 'Examples and explanations of the most common functions available in PyAngelo.';
    $this->assertSame($expectedViewName, $this->response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
