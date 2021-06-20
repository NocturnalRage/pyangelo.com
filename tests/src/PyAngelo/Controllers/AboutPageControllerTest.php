<?php
namespace Tests\src\PyAngelo\Controllers;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\AboutPageController;

class AboutPageControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->controller = new AboutPageController (
      $this->request,
      $this->response,
      $this->auth
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\AboutPageController');
  }

  public function testViewHasBeenSet() {
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'about.html.php';
    $this->assertSame($expectedViewName, $this->response->getView());
  }

  public function testViewMetaDataHasBeenSet() {
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedPageTitle = "About PyAngelo";
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $expectedMetaDescription = "My mission with PyAngelo is to teach everyone how to code.";
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  public function testPersonDetailsForViewHasBeenSet() {
    $details = [
      'loggedIn' => false,
      'person' => ['person_id' => 0, 'email' => 'fred@hotmail.com'],
      'isAdmin' => false
    ];
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with()->andReturn($details);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $this->assertSame($details, $responseVars['personInfo']);
  }
}
?>
