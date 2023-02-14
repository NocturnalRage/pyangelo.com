<?php
namespace Tests\src\PyAngelo\Controllers\PasswordReset; 
use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\PasswordReset\ResetPasswordValidateController;
use PyAngelo\Auth\Auth;

class ResetPasswordValidateControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $personRepository;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->personRepository = Mockery::mock('PyAngelo\Repositories\PersonRepository');
    $this->controller = new ResetPasswordValidateController (
      $this->request,
      $this->response,
      $this->auth,
      $this->personRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\PasswordReset\ResetPasswordValidateController');
  }

  public function testRedirectToPasswordPageWhenLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /password'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'You are already logged in so you can simply change your password.';
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testRedirectToForgotPasswordPageWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /forgot-password';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'Something seems wrong here. Can you please restart the process to reset your password.';
    $this->assertEquals($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testRedirectToForgotPasswordPageWhenNoToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->request->post = [];
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /forgot-password';
    $expectedHeaders = array(array('header', $expectedLocation));
    $expectedFlashMessage = 'Something seems wrong here. Can you please restart the process to reset your password.';
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertEquals($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testRedirectToForgotPasswordPageWhenInvalidToken() {
    $token = 'invalid-reset-token';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->personRepository->shouldReceive('getPasswordResetRequest')
      ->once()
      ->with($token)
      ->andReturn(false);
    $this->request->post = ['token' => $token];
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /forgot-password';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'Something seems wrong here. Can you please restart the process to reset your password.';
    $this->assertEquals($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testRedirectToResetPasswordWhenInvalidPassword() {
    session_start();
    $personId = 99;
    $token = 'valid-token';
    $invalidPassword = 'abc';
    $this->request->post = ['token' => $token, 'loginPassword' => $invalidPassword];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->personRepository->shouldReceive('getPasswordResetRequest')
      ->once()
      ->with($token)
      ->andReturn(['person_id' => $personId]);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /reset-password?token=' . $token;
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedErrors = [
      'loginPassword' => 'The password must be between 4 characters and 30 characters long.'
    ];
    $this->assertEquals($expectedErrors, $_SESSION['errors']);
  }

  public function testRedirectToLoginPageWhenPasswordSuccess() {
    $personId = 99;
    $token = 'valid-token';
    $validPassword = 'secret';
    $this->request->post = [
      'token' => $token,
      'loginPassword' => $validPassword
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->personRepository->shouldReceive('getPasswordResetRequest')
      ->once()
      ->with($token)
      ->andReturn(['person_id' => $personId]);
    $this->personRepository->shouldReceive('updatePassword')
      ->once()
      ->with($personId, $validPassword);
    $this->personRepository->shouldReceive('processPasswordResetRequest')
      ->once()
      ->with($personId, $token);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /login';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
