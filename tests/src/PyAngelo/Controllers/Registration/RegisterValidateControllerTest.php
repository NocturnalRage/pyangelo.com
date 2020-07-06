<?php
namespace tests\src\PyAngelo\Controllers\Registration;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Registration\RegisterValidateController;

class RegisterValidateControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->registerFormService = Mockery::mock('PyAngelo\FormServices\RegisterFormService');
    $this->controller = new RegisterValidateController (
      $this->request,
      $this->response,
      $this->auth,
      $this->registerFormService
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Registration\RegisterValidateController');
  }

  /**
   * @runInSeparateProcess
   */
  public function testRedirectsToHomePageWhenLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('You are already logged in!', $this->request->session['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testRedirectsToRegisterPageWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /register'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('Please register from the PyAngelo website!', $this->request->session['flash']['message']);
  }

  public function testRedirectsToRegisterWhenFormFilledInTooQuickly() {
    $dotenv = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../../../..', '.env.test');
    $dotenv->load();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $email = 'fastfreddy@hotmail.com';
    $this->request->post = [
      'email' => $email,
      'time' => time(),
      'givenName' => 'Fast',
      'familyName' => 'Freddy',
      'email' => 'fast@hotmail.com'
    ];
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /register'));
    $expectedFlashMessage = 'Please register manually from the PyAngelo website!';
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertEquals($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testRedirectWhenPersonCannotBeCreated() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->registerFormService->shouldReceive('createPerson')->once()->andReturn(false);
    $expectedErrors = ['foo' => 'bar'];
    $expectedFlash = 'flash message';
    $this->registerFormService->shouldReceive('getErrors')->once()->andReturn($expectedErrors);
    $this->registerFormService->shouldReceive('getFlashMessage')->once()->andReturn($expectedFlash);
    $email = 'fastfreddy@hotmail.com';
    $this->request->post = [
      'email' => $email,
      'time' => time() - 5,
      'givenName' => 'Fast',
      'familyName' => 'Freddy',
      'email' => 'fast@hotmail.com'
    ];
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /register'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertEquals($expectedErrors, $this->request->session['errors']);
    $this->assertEquals($expectedFlash, $this->request->session['flash']['message']);
  }

  public function testRedirectsAfterCreatingPersonSuccessfully() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->registerFormService->shouldReceive('createPerson')->once()->andReturn(true);
    $email = 'fastfreddy@hotmail.com';
    $this->request->post = [
      'email' => $email,
      'time' => time() - 5,
      'givenName' => 'Fast',
      'familyName' => 'Freddy',
      'email' => $email
    ];
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /please-confirm-your-registration?email=' . urlencode($email);
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
