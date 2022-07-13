<?php
namespace tests\src\PyAngelo\Controllers\Profile;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Profile\ToggleCancelSubscriptionController;

class ToggleCancelSubscriptionControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->stripeWrapper = Mockery::mock('Framework\Billing\StripeWrapper');
    $this->stripeRepository = Mockery::mock('PyAngelo\Repositories\StripeRepository');
    $this->email = Mockery::mock('PyAngelo\Email\WhyCancelEmail');
    $this->controller = new ToggleCancelSubscriptionController(
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Profile\ToggleCancelSubscriptionController');
  }

  public function testToggleSubscriptionControllerWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $expectedFlashMessage = "You must be logged in to update your subscription.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testToggleSubscriptionControllerInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /subscription'));
    $expectedFlashMessage = "Please update your subscription from the PyAngelo website.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testToggleSubscriptionControllerWithNoSubscription() {
    $personId = 8;
    $givenName = 'Fred';
    $person = [
      'person_id' => $personId,
      'given_name' => $givenName
    ];
    $testStripeId = 'TEST-STRIPE-ID';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->stripeRepository->shouldReceive('getCurrentSubscription')->with($personId)->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /subscription'));
    $expectedFlashMessage = 'Sorry, we could not update your subscription. Please try again, or contact us. Here was the error message: You do not have an active subscription.';
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testCancelActiveSubscription() {
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
      'cancel_at_period_end' => 0,
      'status' => 'active'
    ];
    $stripeSubscription = (object) [
      'id' => $subscriptionId,
      'cancel_at_period_end' => false,
      'status' => 'active'
    ];
    $updatedSubscription = (object) [
      'id' => $subscriptionId,
      'cancel_at_period_end' => true,
      'current_period_start' => 1657687627,
      'current_period_end' => 1657687927,
      'status' => 'canceled'
    ];
    $testStripeId = 'TEST-STRIPE-ID';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->stripeRepository->shouldReceive('getCurrentSubscription')->with($personId)->andReturn($subscription);
    $this->stripeWrapper->shouldReceive('retrieveSubscription')->with($subscriptionId)->andReturn($stripeSubscription);
    $this->stripeWrapper
         ->shouldReceive('updateSubscription')
         ->with($subscriptionId, ['cancel_at_period_end' => true])
         ->andReturn($updatedSubscription);
    $this->email->shouldReceive('queueEmail')->once()->with($mailInfo);
    $this->stripeRepository
         ->shouldReceive('updateSubscription')
         ->with(
           $updatedSubscription->id,
           $updatedSubscription->cancel_at_period_end,
           $updatedSubscription->current_period_start,
           $updatedSubscription->current_period_end,
           $updatedSubscription->status
         )
         ->andReturn(1);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /subscription'));
    $expectedFlashMessage = 'Your subscription has been canceled.';
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testResumeCanceledSubscription() {
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
      'cancel_at_period_end' => 1,
      'status' => 'active'
    ];
    $stripeSubscription = (object) [
      'id' => $subscriptionId,
      'cancel_at_period_end' => true,
      'status' => 'active'
    ];
    $updatedSubscription = (object) [
      'id' => $subscriptionId,
      'cancel_at_period_end' => false,
      'current_period_start' => 1657687627,
      'current_period_end' => 1657687927,
      'status' => 'active'
    ];
    $testStripeId = 'TEST-STRIPE-ID';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->stripeRepository->shouldReceive('getCurrentSubscription')->with($personId)->andReturn($subscription);
    $this->stripeWrapper->shouldReceive('retrieveSubscription')->with($subscriptionId)->andReturn($stripeSubscription);
    $this->stripeWrapper
         ->shouldReceive('updateSubscription')
         ->with($subscriptionId, ['cancel_at_period_end' => false])
         ->andReturn($updatedSubscription);


    $this->stripeRepository
         ->shouldReceive('updateSubscription')
         ->with(
           $updatedSubscription->id,
           $updatedSubscription->cancel_at_period_end,
           $updatedSubscription->current_period_start,
           $updatedSubscription->current_period_end,
           $updatedSubscription->status
         )
         ->andReturn(1);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /subscription'));
    $expectedFlashMessage = 'Your subscription has been resumed.';
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }
}
?>
