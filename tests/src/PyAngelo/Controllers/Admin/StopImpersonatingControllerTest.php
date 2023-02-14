<?php
namespace tests\src\PyAngelo\Controllers\Admin;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Admin\StopImpersonatingController;

class StopImpersonatingControllerTest extends TestCase {
  protected $personRepository;
  protected $request;
  protected $response;
  protected $auth;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->personRepository = Mockery::mock('PyAngelo\Repositories\PersonRepository');
    $this->controller = new StopImpersonatingController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Admin\StopImpersonatingController');
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenNoImpersonator() {
    session_start();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "There is no impersonator!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenInvalidImpersonator() {
    session_start();
    $email = 'invalid@email.com';
    $_SESSION['impersonator'] = $email;
    $this->personRepository->shouldReceive('getPersonByEmail')
      ->once()
      ->with($email)
      ->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You must be a valid impersonator!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenValidImpersonator() {
    session_start();
    $_SESSION['loginEmail'] = "auser@email.com";
    $impersonatorPersonId = 1;
    $impersonatorEmail = 'valid@email.com';
    $impersonator = [
      'person_id' => $impersonatorPersonId,
      'email' => $impersonatorEmail
    ];
    $_SESSION['impersonator'] = $impersonatorEmail;

    $this->personRepository->shouldReceive('getPersonByEmail')
      ->once()
      ->with($impersonatorEmail)
      ->andReturn($impersonator);

    $response = $this->controller->exec();
    $this->assertSame($impersonatorEmail, $_SESSION['loginEmail']);
    $this->assertTrue(!isset($_SESSION['impersonator']));

    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /admin'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
