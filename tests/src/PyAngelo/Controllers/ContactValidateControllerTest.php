<?php
namespace Tests\src\PyAngelo\Controllers;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\ContactValidateController;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class ContactValidateControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $contactUsEmail;
  protected $turnstileVerifier;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->contactUsEmail = Mockery::mock('PyAngelo\Email\ContactUsEmail');
    $this->turnstileVerifier = Mockery::mock('Framework\Turnstile\TurnstileVerifier');

    $this->controller = new ContactValidateController (
      $this->request,
      $this->response,
      $this->auth,
      $this->contactUsEmail,
      $this->turnstileVerifier
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\ContactValidateController');
  }

  public function testInvalidCrsfToken() {
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /contact'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('Please contact us from the PyAngelo website!', $_SESSION['flash']['message']);
  }

  public function testNoTurnstile() {
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /contact'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('Cloudflare turnstile could not verify you were a human. Please try again.', $_SESSION['flash']['message']);
  }

  public function testTurnstileRejected() {
    $name = 'Fast Freddy';
    $email = 'fastfreddy@hotmail.com';
    $inquiry = 'How to I become faster?';
    $turnstileResponse = 'fake response';
    $ipAddress = '127.0.0.1';
    $this->request->server['REMOTE_ADDR'] = $ipAddress;
    $this->request->server['SERVER_NAME'] = "pyangelo.com";
    $this->request->post = [
      'name' => $name,
      'email' => $email,
      'inquiry' => $inquiry,
      'cf-turnstile-response' => $turnstileResponse
    ];

    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->turnstileVerifier->shouldReceive('verify')
      ->once()
      ->with($turnstileResponse, $ipAddress)
      ->andReturn(['failed']);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /contact'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('Cloudflare turnstile could not verify you were a human. Please try again.', $_SESSION['flash']['message']);
  }

  public function testContactUsSuccessfully() {
    $name = 'Fast Freddy';
    $email = 'fastfreddy@hotmail.com';
    $inquiry = 'How to I become faster?';
    $turnstileResponse = 'fake response';
    $ipAddress = '127.0.0.1';
    $this->request->server['REMOTE_ADDR'] = $ipAddress;
    $this->request->server['SERVER_NAME'] = "pyangelo.com";
    $this->request->post = [
      'name' => $name,
      'email' => $email,
      'inquiry' => $inquiry,
      'cf-turnstile-response' => $turnstileResponse
    ];
    $mailInfo = [
      'name' => $name,
      'replyEmail' => $email,
      'inquiry' => $inquiry
    ];

    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->turnstileVerifier->shouldReceive('verify')
      ->once()
      ->with($turnstileResponse, $ipAddress)
      ->andReturn(['ok' => 'true']);
    $this->contactUsEmail->shouldReceive('sendEmail')
      ->once()
      ->with($mailInfo)
      ->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /contact-receipt';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  #[RunInSeparateProcess]
  public function testContactUsWithErrors() {
    session_start();
    $name = 'Fast Freddy';
    $email = 'fastfreddy@hotmail.com';
    $inquiry = 'How to I become faster?';
    $turnstileResponse = 'fake response';
    $ipAddress = '127.0.0.1';
    $this->request->server['REMOTE_ADDR'] = $ipAddress;
    $this->request->server['SERVER_NAME'] = "pyangelo.com";
    $this->request->post = [
      'email' => $email,
      'inquiry' => $inquiry,
      'cf-turnstile-response' => $turnstileResponse
    ];
    $mailInfo = [
      'name' => $name,
      'replyEmail' => $email,
      'inquiry' => $inquiry
    ];

    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->turnstileVerifier->shouldReceive('verify')
      ->once()
      ->with($turnstileResponse, $ipAddress)
      ->andReturn(['ok' => true]);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /contact';
    $expectedHeaders = array(array('header', $expectedLocation));
    $expectedErrors = [ 'name' => 'Please enter your name.' ];
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('There were some errors. Please fix these and resubmit your inquiry.', $_SESSION['flash']['message']);
    $this->assertSame($expectedErrors, $_SESSION['errors']);
  }
}
?>
