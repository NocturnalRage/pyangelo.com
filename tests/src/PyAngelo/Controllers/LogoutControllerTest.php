<?php
namespace Tests\src\PyAngelo\Controllers;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\LogoutController;

class LogoutControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->controller = new LogoutController (
      $this->request,
      $this->response,
      $this->auth
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\LogoutController');
  }

  public function testRedirectsToHomePageWhenAlreadyLoggedOut() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $this->response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('You are already logged out!', $_SESSION['flash']['message']);
  }

  public function testRedirectsToHomePageWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $this->response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('You need to logout from the PyAngelo website!', $_SESSION['flash']['message']);
  }
  /**
    *    * @runInSeparateProcess
    *       */
  public function testWhenLoggedInWithInvalidCrsfToken() {
    session_start();
    $request = new Request($GLOBALS);
    $auth = Mockery::mock('PyAngelo\Auth\Auth');
    $auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);
    $response = new Response('views');
    $controller = new LogoutController (
      $request,
      $response,
      $auth
    );
    $this->response = $controller->exec();
    $responseVars = $this->response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $this->response->getHeaders());
  }

  /**
    ** @runInSeparateProcess
    **/
  public function testWhenLoggedInWithValidCrsfToken() {
    $person = [
      'person_id' => 999,
      'given_name' => 'Fred',
      'family_name' => 'Fast',
      'email' => 'fastfred@hotmail.com'
    ];
    session_start();
    $request = new Request($GLOBALS);
    $_SESSION['loginEmail'] = 'fastfred@hotmail.com';
    $auth = Mockery::mock('PyAngelo\Auth\Auth');
    $auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $auth->shouldReceive('person')->once()->with()->andReturn($person);
    $auth->shouldReceive('deleteRememberMe')
      ->once()
      ->with($person['person_id'])
      ->andReturn($person);
    $response = new Response('views');
    $controller = new LogoutController (
      $request,
      $response,
      $auth
    );
    $this->response = $controller->exec();
    $responseVars = $this->response->getVars();
    $expectedRedirectHeader = array('header', 'Location: /login');
    $actualHeaders = $this->response->getHeaders();
    $this->assertSame($expectedRedirectHeader, $actualHeaders[3]);
    $this->assertSame('rememberme', $actualHeaders[0][1]);
    $this->assertSame('', $actualHeaders[0][2]);
    $this->assertTrue($actualHeaders[0][7]);
    $this->assertSame('remembermesession', $actualHeaders[1][1]);
    $this->assertSame('', $actualHeaders[1][2]);
    $this->assertTrue($actualHeaders[1][7]);
    $this->assertSame('remembermetoken', $actualHeaders[2][1]);
    $this->assertSame('', $actualHeaders[2][2]);
    $this->assertTrue($actualHeaders[2][7]);
    $this->assertEmpty($_SESSION);
  }
}
?>
