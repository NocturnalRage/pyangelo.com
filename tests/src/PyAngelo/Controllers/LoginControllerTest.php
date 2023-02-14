<?php
namespace Tests\src\PyAngelo\Controllers;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\LoginController;

class LoginControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $recaptchaKey;
  protected $controller;


  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->recaptchaKey = "recaptcha";
    $this->controller = new LoginController (
      $this->request,
      $this->response,
      $this->auth,
      $this->recaptchaKey
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\LoginController');
  }

  public function testRedirectsHomeWhenLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('You are already logged in!', $_SESSION['flash']['message']);
  }

  public function testBasicLoginView() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();

    $response = $this->controller->exec();
    $responseVars = $this->response->getVars();
    $expectedViewName = 'login.html.php';
    $expectedPageTitle = 'PyAngelo Login';
    $expectedMetaDescription = "Login to the PyAngelo website.";
    $this->assertSame($expectedViewName, $this->response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
