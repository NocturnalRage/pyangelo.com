<?php
namespace tests\src\PyAngelo\Controllers\Membership;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Membership\ProcessSubscriptionController;

class ProcessSubscriptionControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->stripeWrapper = Mockery::mock('Framework\Billing\StripeWrapper');
    $this->stripeRepository = Mockery::mock('PyAngelo\Repositories\StripeRepository');
    $this->controller = new ProcessSubscriptionController(
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Membership\ProcessSubscriptionController');
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/create-subscription.json.php';
    $expectedStatus = 'danger';
    $expectedMessage = 'You must be logged in to become a premium member.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/create-subscription.json.php';
    $expectedStatus = 'danger';
    $expectedMessage = 'You must become a premium member from the PyAngelo website.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoPriceId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/create-subscription.json.php';
    $expectedStatus = 'danger';
    $expectedMessage = 'You must select a price for the subscription.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenHasActiveSubscription() {
    $testStripePriceId = "test-price";
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(true);

    $this->request->post["priceId"] = $testStripePriceId;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/create-subscription.json.php';
    $expectedStatus = 'danger';
    $expectedMessage = 'You still have an active subscription.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNotYetStripeCustomerAndNoSubscription() {
    $testStripePriceId = "test-price";
    $testCustomerId = 'TEST-CUSTOMER';
    $clientSecret = 'TOP-SECRET';
    $person = [
      'person_id' => 1,
      'email' => 'joel@geelongfc.com.au',
      'given_name' => 'Joel',
      'family_name' => 'Selwood'
    ];
    $customer = (object) [
      'id' => $testCustomerId
    ];
    $paymentIntent = (object) [
      'client_secret' => $clientSecret
    ];
    $latestInvoice = (object) [
      'payment_intent' => $paymentIntent
    ];
    $testSubscriptionId = 'TEST-SUBSCRIPTION';
    $testSubscription = (object) [
      'id' => $testSubscriptionId,
      'current_period_start' => '2021-07-22 17:08:30',
      'current_period_end' => '2021-08-22 17:08:30',
      'customer' => $testCustomerId,
      'price_id' => $testStripePriceId,
      'latest_invoice' => $latestInvoice,
      'client_secret' => $clientSecret,
      'start_date' => '2021-07-22:17:09:00',
      'status' => 'incomplete',
      0
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('stripeCustomerId')->once()->with()->andReturn(NULL);
    $this->stripeWrapper->shouldReceive('createCustomer')
                        ->once()
                        ->with($person['email'], $person['given_name'] . " " . $person['family_name'])
                        ->andReturn($customer);
    $this->stripeRepository->shouldReceive('updateStripeCustomerId')->once()->with($person['person_id'], $testCustomerId)->andReturn(NULL);
    $this->stripeRepository->shouldReceive('getIncompleteSubscription')->once()->with($person['person_id'], $testStripePriceId)->andReturn(NULL);
    $this->stripeWrapper->shouldReceive('createSubscription')->once()->with($testCustomerId, $testStripePriceId)->andReturn($testSubscription);
    $this->stripeRepository->shouldReceive('insertSubscription')->once()->andReturn(1);

    $this->request->post["priceId"] = $testStripePriceId;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/create-subscription.json.php';
    $expectedStatus = 'success';
    $expectedMessage = 'Subscription created.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenStripeCustomerAndSubscription() {
    $testStripePriceId = "test-price";
    $testCustomerId = 'TEST-CUSTOMER';
    $clientSecret = 'TOP-SECRET';
    $person = [
      'person_id' => 1,
      'email' => 'joel@geelongfc.com.au',
      'given_name' => 'Joel',
      'family_name' => 'Selwood'
    ];
    $customer = (object) [
      'id' => $testCustomerId
    ];
    $paymentIntent = (object) [
      'client_secret' => $clientSecret
    ];
    $latestInvoice = (object) [
      'payment_intent' => $paymentIntent
    ];
    $testSubscriptionId = 'TEST-SUBSCRIPTION';
    $incompleteSubscription = [
      'id' => $testSubscriptionId,
      'current_period_start' => '2021-07-22 17:08:30',
      'current_period_end' => '2021-08-22 17:08:30',
      'customer' => $testCustomerId,
      'price_id' => $testStripePriceId,
      'stripe_client_secret' => $clientSecret,
      'start_date' => '2021-07-22:17:09:00',
      'status' => 'incomplete',
      0
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('stripeCustomerId')->once()->with()->andReturn(NULL);
    $this->stripeWrapper->shouldReceive('createCustomer')
                        ->once()
                        ->with($person['email'], $person['given_name'] . " " . $person['family_name'])
                        ->andReturn($customer);
    $this->stripeRepository->shouldReceive('updateStripeCustomerId')->once()->with($person['person_id'], $testCustomerId)->andReturn(NULL);
    $this->stripeRepository->shouldReceive('getIncompleteSubscription')->once()->with($person['person_id'], $testStripePriceId)->andReturn($incompleteSubscription);

    $this->request->post["priceId"] = $testStripePriceId;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/create-subscription.json.php';
    $expectedStatus = 'success';
    $expectedMessage = 'Subscription created.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }
}
?>
