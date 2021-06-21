<?php
namespace Tests\src\PyAngelo\Controllers;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\LoginValidateController;
use PyAngelo\Auth\Auth;

class LoginValidateControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->recaptcha = Mockery::mock('Framework\Recaptcha\RecaptchaClient');
    $this->controller = new LoginValidateController (
      $this->request,
      $this->response,
      $this->auth,
      $this->recaptcha
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\LoginValidateController');
  }

  public function testWhenLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('You are already logged in!', $this->request->session['flash']['message']);
  }

  public function testRedirectToLoginPageWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /login';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'Please log in from the PyAngelo website.';
    $this->assertEquals($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testRedirectToLoginPageWhenNoFormData() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->request->post = [];
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /login';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedErrors = [
      'email' => 'You must enter your email to log in.',
      'loginPassword' => 'You must enter a password to log in.'

    ];
    $this->assertEquals($expectedErrors, $this->request->session['errors']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testRedirectToLoginPageWhenInvalidEmail() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->request->post = [
      'email' => 'fredhotmail.com'
    ];
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /login';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedErrors = [
      'email' => 'You did not enter a valid email address.',
      'loginPassword' => 'You must enter a password to log in.'

    ];
    $this->assertEquals($expectedErrors, $this->request->session['errors']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testNoRecaptcha() {
    session_start();
    $email = 'fastfreddy@hotmail.com';
    $password = 'secret';
    $this->request->post = [
      'email' => $email,
      'loginPassword' => $password
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'Login could not be validated. Please ensure you are a human!';
    $this->assertEquals($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testInvalidRecaptcha() {
    session_start();
    $recaptchaResponse = 'Fake Response';
    $ipAddress = '127.0.0.1';
    $serverName = 'pyangelo.com';
    $this->request->server['REMOTE_ADDR'] = $ipAddress;
    $this->request->server['SERVER_NAME'] = $serverName;
    $email = 'fastfreddy@hotmail.com';
    $password = 'secret';
    $this->request->post = [
      'email' => $email,
      'loginPassword' => $password,
      'g-recaptcha-response' => $recaptchaResponse
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->recaptcha->shouldReceive('verified')
      ->once()
      ->with($serverName, 'loginwithversion3', $recaptchaResponse, $ipAddress)
      ->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'Login could not be checked. Please ensure you are a human!';
    $this->assertEquals($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testRedirectTOLoginWhenInvalidUsernameOrPassword() {
    session_start();
    $recaptchaResponse = 'Fake Response';
    $ipAddress = '127.0.0.1';
    $serverName = 'pyangelo.com';
    $this->request->server['REMOTE_ADDR'] = $ipAddress;
    $this->request->server['SERVER_NAME'] = $serverName;
    $email = 'fastfreddy@hotmail.com';
    $password = 'secret';
    $this->request->post = [
      'email' => $email,
      'loginPassword' => $password,
      'g-recaptcha-response' => $recaptchaResponse
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->recaptcha->shouldReceive('verified')
      ->once()
      ->with($serverName, 'loginwithversion3', $recaptchaResponse, $ipAddress)
      ->andReturn(true);
    $this->auth->shouldReceive('authenticateLogin')
      ->once()
      ->with($email, $password)
      ->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'The email and password do not match. Login failed.';
    $this->assertEquals($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testLoginWithoutRememberMe() {
    session_start();
    $recaptchaResponse = 'Fake Response';
    $ipAddress = '127.0.0.1';
    $serverName = 'pyangelo.com';
    $this->request->server['REMOTE_ADDR'] = $ipAddress;
    $this->request->server['SERVER_NAME'] = $serverName;
    $email = 'fastfreddy@hotmail.com';
    $password = 'secret';
    $this->request->post = [
      'email' => $email,
      'loginPassword' => $password,
      'g-recaptcha-response' => $recaptchaResponse
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->recaptcha->shouldReceive('verified')
      ->once()
      ->with($serverName, 'loginwithversion3', $recaptchaResponse, $ipAddress)
      ->andReturn(true);
    $this->auth->shouldReceive('authenticateLogin')
      ->once()
      ->with($email, $password)
      ->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlash = "You are now logged in";
    $this->assertEquals($expectedFlash, $this->request->session['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testLoginWithRedirectWithoutRememberMe() {
    session_start();
    $recaptchaResponse = 'Fake Response';
    $ipAddress = '127.0.0.1';
    $serverName = 'pyangelo.com';
    $this->request->server['REMOTE_ADDR'] = $ipAddress;
    $this->request->server['SERVER_NAME'] = $serverName;
    $redirect = '/tutorials';
    $this->request->session['redirect'] = $redirect;
    $email = 'fastfreddy@hotmail.com';
    $password = 'secret';
    $this->request->post = [
      'email' => $email,
      'loginPassword' => $password,
      'g-recaptcha-response' => $recaptchaResponse
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->recaptcha->shouldReceive('verified')
      ->once()
      ->with($serverName, 'loginwithversion3', $recaptchaResponse, $ipAddress)
      ->andReturn(true);
    $this->auth->shouldReceive('authenticateLogin')
      ->once()
      ->with($email, $password)
      ->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: ' . $redirect));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlash = "You are now logged in";
    $this->assertEquals($expectedFlash, $this->request->session['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testLoginSuccessWithRememberMe() {
    session_start();
    $recaptchaResponse = 'Fake Response';
    $ipAddress = '127.0.0.1';
    $serverName = 'pyangelo.com';
    $this->request->server['REMOTE_ADDR'] = $ipAddress;
    $this->request->server['SERVER_NAME'] = $serverName;
    $personId = 99;
    $email = 'fastfreddy@hotmail.com';
    $password = 'secret';
    $this->request->post = [
      'person_id' => $personId,
      'email' => $email,
      'loginPassword' => $password,
      'rememberme' => 'y',
      'g-recaptcha-response' => $recaptchaResponse
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->recaptcha->shouldReceive('verified')
      ->once()
      ->with($serverName, 'loginwithversion3', $recaptchaResponse, $ipAddress)
      ->andReturn(true);
    $this->auth->shouldReceive('authenticateLogin')
      ->once()
      ->with($email, $password)
      ->andReturn(true);
    $this->auth->shouldReceive('person')
      ->once()
      ->with()
      ->andReturn(['person_id' => $personId]);
    $this->auth->shouldReceive('insertRememberMe')
      ->once()
      ->andReturn(1);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedRedirectHeader = array('header', 'Location: /');
    $headers = $response->getHeaders();
    // Cookies should be set in header
    $expectedPersonId = $headers[0][2];
    $this->assertSame($expectedPersonId, $personId);
    $sessionId = $headers[1][2];
    $token = $headers[2][2];
    $redirectHeader = $headers[3];
    $this->assertSame($expectedRedirectHeader, $redirectHeader);
    $expectedFlash = "You are now logged in";
    $this->assertEquals($expectedFlash, $this->request->session['flash']['message']);
  }
}
?>
