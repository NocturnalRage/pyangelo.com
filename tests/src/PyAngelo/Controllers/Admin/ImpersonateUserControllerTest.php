<?php
namespace tests\src\PyAngelo\Controllers\Admin;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Admin\ImpersonateUserController;

class ImpersonateUserControllerTest extends TestCase {
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
    $this->controller = new ImpersonateUserController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Admin\ImpersonateUserController');
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenNotAdmin() {
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenAdminNoEmail() {
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /admin/users'));
    $expectedFlashMessage = "You must select a person to impersonate!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenAdminInvalidEmail() {
    $email = 'fred@hotmail.com';
    session_start();
    $this->request->post['email'] = $email;
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->personRepository->shouldReceive('getPersonByEmail')
      ->once()
      ->with($email)
      ->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /admin/users'));
    $expectedFlashMessage = "You must select a valid person to impersonate!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenAdminValidEmail() {
    $personId = 100;
    $email = 'fred@hotmail.com';
    $person = [
      'person_id' => $personId,
      'email' => $email,
    ];
    $adminPersonId = 1;
    $adminEmail = 'admin@hotmail.com';
    $admin = [
      'person_id' => $adminPersonId,
      'email' => $adminEmail
    ];
    $this->request->post['email'] = $email;
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->personRepository->shouldReceive('getPersonByEmail')
      ->once()
      ->with($email)
      ->andReturn($person);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($admin);

    $response = $this->controller->exec();
    $this->assertSame($email, $_SESSION['loginEmail']);
    $this->assertSame($adminEmail, $_SESSION['impersonator']);

    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
