<?php
namespace tests\src\PyAngelo\Controllers\Profile;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Profile\PaymentMethodUpdateController;

class PaymentMethodUpdateControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $stripeWrapper;
  protected $stripeRepository;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->stripeWrapper = Mockery::mock('Framework\Billing\StripeWrapper');
    $this->stripeRepository = Mockery::mock('PyAngelo\Repositories\StripeRepository');
    $this->controller = new PaymentMethodUpdateController(
      $this->request,
      $this->response,
      $this->auth,
      $this->stripeWrapper,
      $this->stripeRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Profile\PaymentMethodUpdateController');
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/payment-method-update.json.php';
    $expectedStatus = 'danger';
    $expectedMessage = 'You must be logged in to update your payment details.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/payment-method-update.json.php';
    $expectedStatus = 'danger';
    $expectedMessage = 'You must update your payment details from the PyAngelo website.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoActiveSubscription() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/payment-method-update.json.php';
    $expectedStatus = 'danger';
    $expectedMessage = 'You must have an active subscription to update your payment details.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenActiveSubscription() {
    $personId = 99;
    $stripeCustomerId = 'CUS-1';
    $subscriptionId = 'SUB-1';
    $clientSecret = 'SECRET';

    $person = [
      'person_id' => $personId,
      'given_name' => 'Joel',
      'family_name' => 'Selwood',
      'stripe_customer_id' => $stripeCustomerId
    ];
    $subscription = [
      'subscription_id' => $subscriptionId
    ];
    $setupIntent = (object) [
      'client_secret' => $clientSecret
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(true);
    $this->stripeRepository->shouldReceive('getCurrentSubscription')->once()->with($personId)->andReturn($subscription);
    $this->stripeWrapper->shouldReceive('createSetupIntent')->once()->with($stripeCustomerId, $subscriptionId)->andReturn($setupIntent);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/payment-method-update.json.php';
    $expectedStatus = 'success';
    $expectedCustomerName = 'Joel Selwood';
    $expectedClientSecret = $clientSecret;
    $expectedMessage = 'Setup Intent created.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedCustomerName, $responseVars['customerName']);
    $this->assertSame($expectedClientSecret, $responseVars['clientSecret']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }
}
?>
