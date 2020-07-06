<?php
namespace tests\src\PyAngelo\Controllers\PasswordReset;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\PasswordReset\ForgotPasswordController;

class ForgotPasswordControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->controller = new ForgotPasswordController (
      $this->request,
      $this->response,
      $this->auth
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\PasswordReset\ForgotPasswordController');
  }

  public function testRedirectHomeWhenLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('You are already logged in!', $this->request->session['flash']['message']);
  }

  public function testBasicForgotPasswordView() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'password-reset/forgot-password.html.php';
    $expectedPageTitle = 'Forgot Password';
    $expectedMetaDescription = "You've forgotton your password. No problems we can reset it for you.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
