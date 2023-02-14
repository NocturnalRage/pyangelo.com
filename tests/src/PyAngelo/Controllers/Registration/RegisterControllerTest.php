<?php
namespace Tests\src\PyAngelo\Controllers\Registration;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Registration\RegisterController;

class RegisterControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $recaptchaKey;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->recaptchaKey = "recaptcha";
    $this->controller = new RegisterController (
      $this->request,
      $this->response,
      $this->auth,
      $this->recaptchaKey
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Registration\RegisterController');
  }

  public function testRedirectsToHomePageWhenLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testViewHasBeenSet() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'registration/register.html.php';
    $this->assertSame($expectedViewName, $this->response->getView());
  }

  public function testViewMetaDataHasBeenSet() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedPageTitle = "Register for PyAngelo";
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $expectedMetaDescription = "Register for PyAngelo and we'll teach you to program.";
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testErrorsIncluded() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $expectedErrors = ['error1' => 'error'];
    $_SESSION['errors'] = $expectedErrors;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $this->assertSame($expectedErrors, $responseVars['errors']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testFormVarsIncluded() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $expectedFormVars = ['givenName' => 'Fred'];
    $_SESSION['formVars'] = $expectedFormVars;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $this->assertSame($expectedFormVars, $responseVars['formVars']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testFlashIncluded() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $expectedFlash = 'This is a flash message';
    $_SESSION['flash'] = $expectedFlash;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $this->assertSame($expectedFlash, $responseVars['flash']);
  }
}
?>
