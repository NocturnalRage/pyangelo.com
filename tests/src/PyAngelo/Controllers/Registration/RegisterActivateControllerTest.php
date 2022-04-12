<?php
namespace Tests\src\PyAngelo\Controllers\Registration;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Registration\RegisterActivateController;

class RegisterActivateControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->personRepository = Mockery::mock('PyAngelo\Repositories\PersonRepository');
    $this->controller = new RegisterActivateController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Registration\RegisterActivateController');
  }

  public function testRedirectsToHomePageWhenLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('You are already logged in!', $_SESSION['flash']['message']);
  }

  public function testRedirectsToRegisterPageWhenNoToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /register'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('We could not activate your free membership. Please start the registration process again. Your registration token was missing.', $_SESSION['flash']['message']);
  }

  public function testRedirectsToRegisterPageWhenInvalidToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $this->request->get['token'] = 'invalid-token';
    $this->personRepository
         ->shouldReceive('getMembershipActivate')
         ->once()
         ->with($this->request->get['token'])
         ->andReturn(NULL);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /register'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('We could not activate your free membership. Please start the registration process again.', $_SESSION['flash']['message']);
  }

  public function testRedirectsToRegisterPageWhenValidTokenButCouldNotUpdate() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $this->request->get['token'] = 'valid-token';
    $membershipActivate = [
      'person_id' => 99,
      'email' => 'any_email@hotmail.com',
      'token' => $this->request->get['token'],
      'processed' => 0
    ];
    $this->personRepository
         ->shouldReceive('getMembershipActivate')
         ->once()
         ->with($this->request->get['token'])
         ->andReturn($membershipActivate);
    $this->personRepository
         ->shouldReceive('processMembershipActivate')
         ->once()
         ->with($this->request->get['token'])
         ->andReturn(0);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /register'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('We could not activate your free membership. Please start the registration process again.', $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testRedirectsToRegisterPageWhenValidTokenNotYetSubscribed() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('setLoginStatus')->once()->with()->andReturn(false);

    $this->request->get['token'] = 'valid-token';
    $membershipActivate = [
      'person_id' => 99,
      'email' => 'any_email@hotmail.com',
      'token' => $this->request->get['token'],
      'processed' => 0
    ];
    $listId = 1;
    $this->personRepository
         ->shouldReceive('getMembershipActivate')
         ->once()
         ->with($this->request->get['token'])
         ->andReturn($membershipActivate);
    $this->personRepository
         ->shouldReceive('processMembershipActivate')
         ->once()
         ->with($this->request->get['token'])
         ->andReturn(1);
    $this->personRepository
         ->shouldReceive('makeActive')
         ->once()
         ->with($membershipActivate['person_id'])
         ->andReturn(1);
    $this->personRepository
         ->shouldReceive('getSubscriber')
         ->once()
         ->with($listId, $membershipActivate['person_id'])
         ->andReturn(NULL);
    $this->personRepository
         ->shouldReceive('insertSubscriber')
         ->once()
         ->with($listId, $membershipActivate['person_id'])
         ->andReturn(1);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /thanks-for-registering'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  /**
   * @runInSeparateProcess
   */
  public function testRedirectsToRegisterPageWhenValidTokenCurrentlyUnsubscribed() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('setLoginStatus')->once()->with()->andReturn(false);

    $this->request->get['token'] = 'valid-token';
    $membershipActivate = [
      'person_id' => 99,
      'email' => 'any_email@hotmail.com',
      'token' => $this->request->get['token'],
      'processed' => 0
    ];
    $listId = 1;
    $unsubscribedStatus = 2;
    $activeStatus = 1;
    $this->personRepository
         ->shouldReceive('getMembershipActivate')
         ->once()
         ->with($this->request->get['token'])
         ->andReturn($membershipActivate);
    $this->personRepository
         ->shouldReceive('processMembershipActivate')
         ->once()
         ->with($this->request->get['token'])
         ->andReturn(1);
    $this->personRepository
         ->shouldReceive('makeActive')
         ->once()
         ->with($membershipActivate['person_id'])
         ->andReturn(1);
    $this->personRepository
         ->shouldReceive('getSubscriber')
         ->once()
         ->with($listId, $membershipActivate['person_id'])
         ->andReturn(['subscriber_status_id' => $unsubscribedStatus]);
    $this->personRepository
         ->shouldReceive('updateSubscriber')
         ->once()
         ->with($listId, $membershipActivate['person_id'], $activeStatus)
         ->andReturn(1);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /thanks-for-registering'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  /**
   * @runInSeparateProcess
   */
  public function testRedirectsToRegisterPageWhenValidTokenAlreadySubscribed() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('setLoginStatus')->once()->with()->andReturn(false);

    $this->request->get['token'] = 'valid-token';
    $membershipActivate = [
      'person_id' => 99,
      'email' => 'any_email@hotmail.com',
      'token' => $this->request->get['token'],
      'processed' => 0
    ];
    $listId = 1;
    $activeStatus = 1;
    $this->personRepository
         ->shouldReceive('getMembershipActivate')
         ->once()
         ->with($this->request->get['token'])
         ->andReturn($membershipActivate);
    $this->personRepository
         ->shouldReceive('processMembershipActivate')
         ->once()
         ->with($this->request->get['token'])
         ->andReturn(1);
    $this->personRepository
         ->shouldReceive('makeActive')
         ->once()
         ->with($membershipActivate['person_id'])
         ->andReturn(1);
    $this->personRepository
         ->shouldReceive('getSubscriber')
         ->once()
         ->with($listId, $membershipActivate['person_id'])
         ->andReturn(['subscriber_status_id' => $activeStatus]);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /thanks-for-registering'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
