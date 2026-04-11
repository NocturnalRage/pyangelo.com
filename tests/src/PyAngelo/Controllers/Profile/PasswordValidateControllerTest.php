<?php
namespace Tests\src\PyAngelo\Controllers\Profile; 
use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Profile\PasswordValidateController;
use PyAngelo\Auth\Auth;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class PasswordValidateControllerTest extends TestCase {
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
    $this->controller = new PasswordValidateController (
      $this->request,
      $this->response,
      $this->auth,
      $this->personRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testPasswordControllerClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Profile\PasswordValidateController');
  }

  public function testRedirectToLoginPageWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'You must be logged in to change your password.';
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testRedirectsToPasswordPageWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /password';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'Please update your password from the PyAngelo website.';
    $this->assertEquals($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  #[RunInSeparateProcess]
  public function testRedirectToPasswordPageWhenNoCurrentPassword() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /password';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedErrors = [
      'currentPassword' => 'You must enter your current password.'
    ];
    $this->assertEquals($expectedErrors, $_SESSION['errors']);
  }

  #[RunInSeparateProcess]
  public function testRedirectToPasswordPageWhenWrongCurrentPassword() {
    session_start();
    $this->request->post = ['currentPassword' => 'wrongpassword'];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn(99);
    $person = ['person_id' => 99, 'password' => password_hash('oldpassword', PASSWORD_DEFAULT)];
    $this->personRepository->shouldReceive('getPersonById')->once()->with(99)->andReturn($person);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /password';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedErrors = [
      'currentPassword' => 'Your current password is incorrect.'
    ];
    $this->assertEquals($expectedErrors, $_SESSION['errors']);
  }

  #[RunInSeparateProcess]
  public function testRedirectToPasswordPageWhenNoPassword() {
    session_start();
    $currentPassword = 'oldpassword';
    $this->request->post = ['currentPassword' => $currentPassword];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn(99);
    $person = ['person_id' => 99, 'password' => password_hash($currentPassword, PASSWORD_DEFAULT)];
    $this->personRepository->shouldReceive('getPersonById')->once()->with(99)->andReturn($person);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /password';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedErrors = [
      'loginPassword' => 'You must supply a password in order to change it.'
    ];
    $this->assertEquals($expectedErrors, $_SESSION['errors']);
  }

  #[RunInSeparateProcess]
  public function testRedirectToPasswordPageWhenInvalidPassword() {
    session_start();
    $currentPassword = 'oldpassword';
    $invalidPassword = 'abc';
    $this->request->post = ['currentPassword' => $currentPassword, 'loginPassword' => $invalidPassword];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn(99);
    $person = ['person_id' => 99, 'password' => password_hash($currentPassword, PASSWORD_DEFAULT)];
    $this->personRepository->shouldReceive('getPersonById')->once()->with(99)->andReturn($person);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /password';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedErrors = [
      'loginPassword' => 'The password must be between 8 characters and 30 characters long.'
    ];
    $this->assertEquals($expectedErrors, $_SESSION['errors']);
  }

  public function testUpdatePasswordSuccess() {
    $personId = 99;
    $currentPassword = 'oldpassword';
    $validPassword = 'newsecretpassword';
    $this->request->post = ['currentPassword' => $currentPassword, 'loginPassword' => $validPassword];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $person = ['person_id' => $personId, 'password' => password_hash($currentPassword, PASSWORD_DEFAULT)];
    $this->personRepository->shouldReceive('getPersonById')->once()->with($personId)->andReturn($person);
    $this->personRepository->shouldReceive('updatePassword')
      ->once()
      ->with($personId, $validPassword);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /profile';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
