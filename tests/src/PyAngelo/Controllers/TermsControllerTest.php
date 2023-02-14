<?php
namespace Tests\src\PyAngelo\Controllers;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\TermsController;

class TermsControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->controller = new TermsController (
      $this->request,
      $this->response,
      $this->auth
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\TermsController');
  }

  public function testViewHasBeenSet() {
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'terms-of-use.html.php';
    $this->assertSame($expectedViewName, $this->response->getView());
  }

  public function testViewMetaDataHasBeenSet() {
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedPageTitle = "Terms of Use | PyAngelo";
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $expectedMetaDescription = "The PyAngelo Terms of Use.";
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
