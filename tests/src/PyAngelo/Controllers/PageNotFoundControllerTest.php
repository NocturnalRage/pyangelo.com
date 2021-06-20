<?php
namespace Tests\src\PyAngelo\Controllers;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\PageNotFoundController;

class PageNotFoundControllerTest extends TestCase {
  public function tearDown(): void {
    Mockery::close();
  }

  public function testPageNotFoundController() {
    $request = new Request($GLOBALS);
    $response = new Response('views');
    $auth = Mockery::mock('PyAngelo\Auth\Auth');
    $auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $controller = new PageNotFoundController (
      $request,
      $response,
      $auth
    );
    $this->response = $controller->exec();
    $responseVars = $this->response->getVars();
    $expectedViewName = 'page-not-found.html.php';
    $expectedPageTitle = 'PyAngelo | Page Not Found';
    $expectedMetaDescription = 'Whoops! We could not find the page your were looking for.';
    $this->assertSame($expectedViewName, $this->response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
