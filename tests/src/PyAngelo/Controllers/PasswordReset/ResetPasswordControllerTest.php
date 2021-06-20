<?php
namespace Tests\src\PyAngelo\Controllers\PasswordReset;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\PasswordReset\ResetPasswordController;

class ResetPasswordControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->controller = new ResetPasswordController (
      $this->request,
      $this->response,
      $this->auth
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\PasswordReset\ResetPasswordController');
  }

  public function testRedirectToHomePageWhenLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'You are already logged in!';
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testRedirectToForgotPasswordPageWhenNoToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /forgot-password'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'We could not reset your password. Please start the process again.';
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testRedirectToForgotPasswordPageWhenInvalidToken() {
    $this->request->get['token'] = 'invalid-token';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('isPasswordResetTokenValid')
      ->once()
      ->with($this->request->get['token'])
      ->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /forgot-password'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'We could not reset your password. Please start the process again.';
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testSuccessWithValidToken() {
    $this->request->get['token'] = 'valid-token';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->auth->shouldReceive('isPasswordResetTokenValid')
      ->once()
      ->with($this->request->get['token'])
      ->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'password-reset/reset-password.html.php';
    $expectedPageTitle = 'Reset Password | PyAngelo';
    $expectedMetaDescription = "Enter your new password.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
