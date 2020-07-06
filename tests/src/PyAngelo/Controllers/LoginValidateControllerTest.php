<?php
namespace tests\src\PyAngelo\Controllers;

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
    $this->controller = new LoginValidateController (
      $this->request,
      $this->response,
      $this->auth
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
  public function testRedirectTOLoginWhenInvalidUsernameOrPassword() {
    session_start();
    $email = 'fastfreddy@hotmail.com';
    $password = 'secret';
    $this->request->post = [
      'email' => $email,
      'loginPassword' => $password
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
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
    $email = 'fastfreddy@hotmail.com';
    $password = 'secret';
    $this->request->post = [
      'email' => $email,
      'loginPassword' => $password
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
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
    $redirect = '/tutorials';
    $this->request->session['redirect'] = $redirect;
    $email = 'fastfreddy@hotmail.com';
    $password = 'secret';
    $this->request->post = [
      'email' => $email,
      'loginPassword' => $password
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
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
    $personId = 99;
    $email = 'fastfreddy@hotmail.com';
    $password = 'secret';
    $this->request->post = [
      'person_id' => $personId,
      'email' => $email,
      'loginPassword' => $password,
      'rememberme' => 'y',
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
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
