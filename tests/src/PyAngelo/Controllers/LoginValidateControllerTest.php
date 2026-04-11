<?php
namespace Tests\src\PyAngelo\Controllers;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\LoginValidateController;
use PyAngelo\Auth\Auth;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class LoginValidateControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $turnstileVerifier;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->turnstileVerifier = Mockery::mock('Framework\Turnstile\TurnstileVerifier');
    $this->controller = new LoginValidateController (
      $this->request,
      $this->response,
      $this->auth,
      $this->turnstileVerifier
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
    $this->assertSame('You are already logged in!', $_SESSION['flash']['message']);
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
    $this->assertEquals($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  #[RunInSeparateProcess]
  public function testRedirectToLoginPageWhenTurnstileFails() {
    session_start();
    $this->request->post = ['cf-turnstile-response' => 'bad-token'];
    $this->request->server['REMOTE_ADDR'] = '127.0.0.1';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->turnstileVerifier->shouldReceive('verify')
      ->once()
      ->with('bad-token', '127.0.0.1')
      ->andReturn(['ok' => false, 'errors' => ['invalid-input-response']]);
    $response = $this->controller->exec();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'Cloudflare turnstile could not verify you were a human. Please try again.';
    $this->assertEquals($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  #[RunInSeparateProcess]
  public function testRedirectToLoginPageWhenTurnstileTokenMissing() {
    session_start();
    $this->request->post = [];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $response = $this->controller->exec();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'Cloudflare turnstile could not verify you were a human. Please try again.';
    $this->assertEquals($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  #[RunInSeparateProcess]
  public function testRedirectToLoginPageWhenNoFormData() {
    session_start();
    $this->request->post = ['cf-turnstile-response' => 'valid-token'];
    $this->request->server['REMOTE_ADDR'] = '127.0.0.1';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->turnstileVerifier->shouldReceive('verify')
      ->once()
      ->with('valid-token', '127.0.0.1')
      ->andReturn(['ok' => true]);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /login';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedErrors = [
      'email' => 'You must enter your email to log in.',
      'loginPassword' => 'You must enter a password to log in.'

    ];
    $this->assertEquals($expectedErrors, $_SESSION['errors']);
  }

  #[RunInSeparateProcess]
  public function testRedirectToLoginPageWhenInvalidEmail() {
    session_start();
    $this->request->post = [
      'cf-turnstile-response' => 'valid-token',
      'email' => 'fredhotmail.com'
    ];
    $this->request->server['REMOTE_ADDR'] = '127.0.0.1';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->turnstileVerifier->shouldReceive('verify')
      ->once()
      ->with('valid-token', '127.0.0.1')
      ->andReturn(['ok' => true]);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /login';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedErrors = [
      'email' => 'You did not enter a valid email address.',
      'loginPassword' => 'You must enter a password to log in.'

    ];
    $this->assertEquals($expectedErrors, $_SESSION['errors']);
  }

  #[RunInSeparateProcess]
  public function testRedirectTOLoginWhenInvalidUsernameOrPassword() {
    session_start();
    $ipAddress = '127.0.0.1';
    $serverName = 'pyangelo.com';
    $this->request->server['REMOTE_ADDR'] = $ipAddress;
    $this->request->server['SERVER_NAME'] = $serverName;
    $email = 'fastfreddy@hotmail.com';
    $password = 'secret';
    $this->request->post = [
      'cf-turnstile-response' => 'valid-token',
      'email' => $email,
      'loginPassword' => $password
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->turnstileVerifier->shouldReceive('verify')
      ->once()
      ->with('valid-token', $ipAddress)
      ->andReturn(['ok' => true]);
    $this->auth->shouldReceive('authenticateLogin')
      ->once()
      ->with($email, $password)
      ->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'The email and password do not match. Login failed.';
    $this->assertEquals($expectedFlashMessage, $_SESSION['flash']['message']);
    $this->assertEquals($this->request->post, $_SESSION['formVars']);
  }

  #[RunInSeparateProcess]
  public function testLoginWithoutRememberMe() {
    session_start();
    $ipAddress = '127.0.0.1';
    $serverName = 'pyangelo.com';
    $this->request->server['REMOTE_ADDR'] = $ipAddress;
    $this->request->server['SERVER_NAME'] = $serverName;
    $email = 'fastfreddy@hotmail.com';
    $password = 'secret';
    $this->request->post = [
      'cf-turnstile-response' => 'valid-token',
      'email' => $email,
      'loginPassword' => $password
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->turnstileVerifier->shouldReceive('verify')
      ->once()
      ->with('valid-token', $ipAddress)
      ->andReturn(['ok' => true]);
    $this->auth->shouldReceive('authenticateLogin')
      ->once()
      ->with($email, $password)
      ->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlash = "You are now logged in";
    $this->assertEquals($expectedFlash, $_SESSION['flash']['message']);
  }

  #[RunInSeparateProcess]
  public function testLoginWithRedirectWithoutRememberMe() {
    session_start();
    $ipAddress = '127.0.0.1';
    $serverName = 'pyangelo.com';
    $this->request->server['REMOTE_ADDR'] = $ipAddress;
    $this->request->server['SERVER_NAME'] = $serverName;
    $redirect = '/tutorials';
    $_SESSION['redirect'] = $redirect;
    $email = 'fastfreddy@hotmail.com';
    $password = 'secret';
    $this->request->post = [
      'cf-turnstile-response' => 'valid-token',
      'email' => $email,
      'loginPassword' => $password
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->turnstileVerifier->shouldReceive('verify')
      ->once()
      ->with('valid-token', $ipAddress)
      ->andReturn(['ok' => true]);
    $this->auth->shouldReceive('authenticateLogin')
      ->once()
      ->with($email, $password)
      ->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: ' . $redirect));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlash = "You are now logged in";
    $this->assertEquals($expectedFlash, $_SESSION['flash']['message']);
  }

  #[RunInSeparateProcess]
  public function testLoginWithUnsafeRedirectGoesToHome() {
    session_start();
    $_SESSION['redirect'] = 'https://evil.com/phish';
    $ipAddress = '127.0.0.1';
    $this->request->server['REMOTE_ADDR'] = $ipAddress;
    $email = 'fastfreddy@hotmail.com';
    $password = 'secret';
    $this->request->post = [
      'cf-turnstile-response' => 'valid-token',
      'email' => $email,
      'loginPassword' => $password
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->turnstileVerifier->shouldReceive('verify')
      ->once()
      ->with('valid-token', $ipAddress)
      ->andReturn(['ok' => true]);
    $this->auth->shouldReceive('authenticateLogin')
      ->once()
      ->with($email, $password)
      ->andReturn(true);
    $response = $this->controller->exec();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertEquals("You are now logged in", $_SESSION['flash']['message']);
  }

  #[RunInSeparateProcess]
  public function testLoginSuccessWithRememberMe() {
    session_start();
    $ipAddress = '127.0.0.1';
    $serverName = 'pyangelo.com';
    $this->request->server['REMOTE_ADDR'] = $ipAddress;
    $this->request->server['SERVER_NAME'] = $serverName;
    $personId = 99;
    $email = 'fastfreddy@hotmail.com';
    $password = 'secret';
    $this->request->post = [
      'cf-turnstile-response' => 'valid-token',
      'person_id' => $personId,
      'email' => $email,
      'loginPassword' => $password,
      'rememberme' => 'y'
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->turnstileVerifier->shouldReceive('verify')
      ->once()
      ->with('valid-token', $ipAddress)
      ->andReturn(['ok' => true]);
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
    $this->assertSame($personId, $headers[0][2]);
    $sessionId = $headers[1][2];
    $token = $headers[2][2];
    // Verify security flags on all three cookies
    foreach ([0, 1, 2] as $i) {
      $opts = $headers[$i][3];
      $this->assertTrue($opts['secure'],            "Cookie $i must have Secure flag");
      $this->assertTrue($opts['httponly'],           "Cookie $i must have HttpOnly flag");
      $this->assertSame('Lax', $opts['samesite'],   "Cookie $i must have SameSite=Lax");
      $this->assertSame('/', $opts['path'],          "Cookie $i must have path=/");
    }
    $redirectHeader = $headers[3];
    $this->assertSame($expectedRedirectHeader, $redirectHeader);
    $expectedFlash = "You are now logged in";
    $this->assertEquals($expectedFlash, $_SESSION['flash']['message']);
  }
}
?>
