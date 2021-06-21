<?php
namespace Tests\src\PyAngelo\Controllers;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\HomePageController;

class HomePageControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->controller = new HomePageController (
      $this->request,
      $this->response,
      $this->auth
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\HomePageController');
  }

  public function testViewHasBeenSetWhenNotLoggedIn() {
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'home.html.php';
    $this->assertSame($expectedViewName, $this->response->getView());
  }

  public function testViewHasBeenSetWhenLoggedIn() {
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'home-logged-in.html.php';
    $this->assertSame($expectedViewName, $this->response->getView());
  }

  public function testViewMetaDataHasBeenSet() {
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedPageTitle = "PyAngelo - Learn To Code";
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $expectedMetaDescription = "Python Graphics Programming in the Browser";
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  public function testPersonDetailsForViewHasBeenSet() {
    $details = [
      'loggedIn' => false,
      'person' => ['person_id' => 0, 'email' => 'fred@hotmail.com'],
      'isAdmin' => false
    ];
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with()->andReturn($details);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $this->assertSame($details, $responseVars['personInfo']);
  }
}
?>
