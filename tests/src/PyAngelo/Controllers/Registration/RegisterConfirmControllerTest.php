<?php
namespace Tests\src\PyAngelo\Controllers\Registration;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Registration\RegisterConfirmController;

class RegisterConfirmControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->controller = new RegisterConfirmController (
      $this->request,
      $this->response,
      $this->auth
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Registration\RegisterConfirmController');
  }

  public function testRedirectsToHomePageWhenLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $flash = ['message' => 'You are already logged in!', 'type' => 'info'];
    $this->assertSame($flash, $_SESSION['flash']);
  }

  public function testViewHasBeenSet() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'registration/please-confirm-your-registration.html.php';
    $this->assertSame($expectedViewName, $this->response->getView());
  }

  public function testViewMetaDataHasBeenSet() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedPageTitle = "Confirm Your Email Address";
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $expectedMetaDescription = "Please confirm your email address and then you'll be a free member of the PyAngelo website.";
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
