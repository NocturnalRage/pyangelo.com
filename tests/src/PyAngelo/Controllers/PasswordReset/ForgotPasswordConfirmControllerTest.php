<?php
namespace tests\src\PyAngelo\Controllers\PasswordReset;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\PasswordReset\ForgotPasswordConfirmController;

class ForgotPasswordConfirmControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->controller = new ForgotPasswordConfirmController (
      $this->request,
      $this->response,
      $this->auth
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\PasswordReset\ForgotPasswordConfirmController');
  }

  public function testRedirectToChangePasswordWhenLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /password'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'You are already logged in so you can simply update your password.';
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testRedirectWhenNoEmailPassedAsGetVariable() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /forgot-password'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = "Sorry, something went wrong, please enter your email address again and we'll send you instructions on how to reset your password.";
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testWhenEmailPassedAsGetVariable() {
    $email = 'any_email@hotmail.com';
    $this->request->get['email'] = $email;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'password-reset/forgot-password-confirm.html.php';
    $expectedPageTitle = 'Request Link Sent';
    $expectedMetaDescription = "If we have the email you entered in our system then a message has been sent with a password reset link.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
