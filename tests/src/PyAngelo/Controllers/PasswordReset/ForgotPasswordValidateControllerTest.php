<?php
namespace Tests\src\PyAngelo\Controllers\PasswordReset; 
use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\PasswordReset\ForgotPasswordValidateController;

class ForgotPasswordValidateControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $forgotPasswordFormService;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->forgotPasswordFormService = Mockery::mock('PyAngelo\FormServices\ForgotPasswordFormService');
    $this->controller = new ForgotPasswordValidateController (
      $this->request,
      $this->response,
      $this->auth,
      $this->forgotPasswordFormService
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\PasswordReset\ForgotPasswordValidateController');
  }

  public function testRedirectToChangePasswordWhenLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /password'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'You are already logged in so you can simply change your password.';
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testRedirectToForgotPasswordWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /forgot-password';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'Please request a password reset from the PyAngelo website.';
    $this->assertEquals($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testForgotPasswordWithNoFormData() {
    session_start();
    $flashMessage = 'There were errors';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->forgotPasswordFormService->shouldReceive('saveRequestAndSendEmail')
      ->once()
      ->with([])
      ->andReturn(false);
    $this->forgotPasswordFormService->shouldReceive('getErrors')
      ->once()
      ->with()
      ->andReturn(['foo' => 'bar']);
    $this->forgotPasswordFormService->shouldReceive('getFlashMessage')
      ->once()
      ->with()
      ->andReturn($flashMessage);
    $this->request->post = [];
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /forgot-password';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertEquals($flashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testForgotPasswordInvalidEmail() {
    session_start();
    $flashMessage = 'There were errors';
    $errors = ['foo' => 'bar'];
    $email = 'fastfreddy.com';
    $this->request->post = ['email' => $email];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->forgotPasswordFormService->shouldReceive('saveRequestAndSendEmail')
      ->once()
      ->with($this->request->post)
      ->andReturn(false);
    $this->forgotPasswordFormService->shouldReceive('getErrors')
      ->once()
      ->with()
      ->andReturn($errors);
    $this->forgotPasswordFormService->shouldReceive('getFlashMessage')
      ->once()
      ->with()
      ->andReturn($flashMessage);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /forgot-password'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertEquals($errors, $_SESSION['errors']);
    $this->assertEquals($flashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testRedirectToConfirmPageOnSuccess() {
    session_start();
    $email = 'fastfreddy@hotmail.com';
    $this->request->post = ['email' => $email];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->forgotPasswordFormService->shouldReceive('saveRequestAndSendEmail')
      ->once()
      ->with($this->request->post)
      ->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /forgot-password-confirm?email=' . urlencode($email);
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
