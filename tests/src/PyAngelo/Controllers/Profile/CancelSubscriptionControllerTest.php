<?php
namespace tests\src\PyAngelo\Controllers\Profile;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Profile\CancelSubscriptionController;

class CancelSubscriptionControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->stripeWrapper = Mockery::mock('Framework\Billing\StripeWrapper');
    $this->stripeRepository = Mockery::mock('PyAngelo\Repositories\StripeRepository');
    $this->email = Mockery::mock('PyAngelo\Email\WhyCancelEmail');
    $this->controller = new CancelSubscriptionController(
      $this->request,
      $this->response,
      $this->auth,
      $this->stripeWrapper,
      $this->stripeRepository,
      $this->email
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Profile\CancelSubscriptionController');
  }

  /**
   * @runInSeparateProcess
   */
  public function testCancelSubscriptionControllerWhenNotLoggedIn() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $expectedFlashMessage = "You must be logged in to cancel your subscription.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testCancelSubscriptionControllerInvalidCrsfToken() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /subscription'));
    $expectedFlashMessage = "Please cancel your subscription from the PyAngelo website.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testCancelSubscriptionControllerWithNoSubscription() {
    $personId = 8;
    $givenName = 'Fred';
    $person = [
      'person_id' => $personId,
      'given_name' => $givenName
    ];
    $testStripeId = 'TEST-STRIPE-ID';
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->stripeRepository->shouldReceive('getCurrentSubscription')->with($personId)->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /subscription'));
    $expectedFlashMessage = 'Sorry, we could not cancel your subscription. Please try again, or contact us. Here was the error message: You do not have an active subscription.';
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testCancelSubscriptionControllerWithValidSubscription() {
    $personId = 8;
    $givenName = 'Fred';
    $person = [
      'person_id' => $personId,
      'given_name' => $givenName,
      'email' => 'anyone@example.com'
    ];
    $mailInfo = [
      'givenName' => $givenName,
      'toEmail' => 'anyone@example.com'
    ];
    $subscriptionId = 'SUB-1';
    $subscription = [
      'subscription_id' => $subscriptionId,
      'status' => 'active'
    ];
    $canceledSubscription = (object) [
      'id' => $subscriptionId,
      'status' => 'canceled'
    ];
    $testStripeId = 'TEST-STRIPE-ID';
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->stripeRepository->shouldReceive('getCurrentSubscription')->with($personId)->andReturn($subscription);
    $this->stripeRepository->shouldReceive('updateSubscriptionStatus')->with($subscriptionId, 'canceled')->andReturn($subscription);
    $this->stripeWrapper->shouldReceive('cancelSubscription')->with($subscriptionId)->andReturn($canceledSubscription);
    $this->email->shouldReceive('queueEmail')->with($mailInfo);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /subscription'));
    $expectedFlashMessage = 'Your subscription has been canceled.';
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }
}
?>
