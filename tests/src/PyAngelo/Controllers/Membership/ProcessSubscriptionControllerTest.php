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
    $expectedStatus = 'login-error';
    $expectedMessage = 'You must be logged in to start a subscription';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenHasActiveSubscription() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/create-subscription.json.php';
    $expectedStatus = 'active-subscription';
    $expectedMessage = 'You have an active subscription';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/create-subscription.json.php';
    $expectedStatus = 'crsf-error';
    $expectedMessage = 'Please sign up from the PyAngelo website';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoPriceId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/create-subscription.json.php';
    $expectedStatus = 'post-error';
    $expectedMessage = 'Price not provided';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenInvalidStripePrice() {
    $stripePriceId = 'STRIPE-TEST-PRICE-ID';
    $this->request->post['priceId'] = $stripePriceId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->stripeWrapper->shouldReceive('retrievePrice')
                        ->once()
                        ->with($stripePriceId)
                        ->andThrow(new \Exception('Price Error'));

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/create-subscription.json.php';
    $expectedStatus = 'stripe-price-error';
    $expectedMessage = 'Price not recognised by Stripe';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoPyangeloPrice() {
    $stripePriceId = 'STRIPE-TEST-PRICE-ID';
    $price = (object) [
      'id' => $stripePriceId
    ];
    $this->request->post['priceId'] = $stripePriceId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->stripeWrapper->shouldReceive('retrievePrice')
                        ->once()
                        ->with($stripePriceId)
                        ->andReturn($price);
    $this->stripeRepository->shouldReceive('getStripePriceById')
                           ->once()
                           ->with($stripePriceId)
                           ->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/create-subscription.json.php';
    $expectedStatus = 'pyangelo-error';
    $expectedMessage = 'Price not recognised by PyAngelo';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenStripeCustomerError() {
    $stripePriceId = 'STRIPE-TEST-PRICE-ID';
    $price = (object) [
      'id' => $stripePriceId
    ];
    $pyangeloPrice = [
      'stripe_price_id' => $stripePriceId
    ];
    $personId = 101;
    $stripeCustId = 'STRIPE-TEST-CUST-ID';
    $person = [
      'person_id' => $personId,
      'email' => 'joel@geelongfc.com.au',
      'given_name' => 'Joel',
      'family_name' => 'Selwood',
      'country_code' => 'AU',
      'stripe_customer_id' => $stripeCustId,
    ];
    $this->request->post['priceId'] = $stripePriceId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->stripeWrapper->shouldReceive('retrievePrice')
                        ->once()
                        ->with($stripePriceId)
                        ->andReturn($price);
    $this->stripeRepository->shouldReceive('getStripePriceById')
                           ->once()
                           ->with($stripePriceId)
                           ->andReturn($pyangeloPrice);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('stripeCustomerId')->once()->with()->andReturn(NULL);
    $this->stripeWrapper->shouldReceive('createCustomer')
                        ->once()
                        ->with(
                          $person['email'],
                          $person['given_name'] . ' ' . $person['family_name']
                        )
                        ->andThrow(new \Exception('Stripe Customer Error'));

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/create-subscription.json.php';
    $expectedStatus = 'stripe-error';
    $expectedMessage = 'Stripe Customer Error';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenStripeCustomerNoSubStripeError() {
    $stripePriceId = 'STRIPE-TEST-PRICE-ID';
    $price = (object) [
      'id' => $stripePriceId
    ];
    $pyangeloPrice = [
      'stripe_price_id' => $stripePriceId
    ];
    $personId = 101;
    $stripeCustId = 'STRIPE-TEST-CUST-ID';
    $person = [
      'person_id' => $personId,
      'email' => 'joel@geelongfc.com.au',
      'given_name' => 'Joel',
      'family_name' => 'Selwood',
      'country_code' => 'AU',
      'stripe_customer_id' => $stripeCustId,
    ];
    $customer = (object) [
      'id' => $stripeCustId
    ];
    $this->request->post['priceId'] = $stripePriceId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->stripeWrapper->shouldReceive('retrievePrice')
                        ->once()
                        ->with($stripePriceId)
                        ->andReturn($price);
    $this->stripeRepository->shouldReceive('getStripePriceById')
                           ->once()
                           ->with($stripePriceId)
                           ->andReturn($pyangeloPrice);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('stripeCustomerId')->once()->with()->andReturn(NULL);
    $this->stripeWrapper->shouldReceive('createCustomer')
                        ->once()
                        ->with(
                          $person['email'],
                          $person['given_name'] . ' ' . $person['family_name']
                        )
                        ->andReturn($customer);
    $this->stripeRepository->shouldReceive('updateStripeCustomerId')
                           ->once()
                           ->with($person['person_id'], $stripeCustId)
                           ->andReturn(1);
    $this->stripeRepository->shouldReceive('getIncompleteSubscription')
                           ->once()
                           ->with($person['person_id'], $stripePriceId)
                           ->andReturn(NULL);
    $this->stripeWrapper->shouldReceive('createSubscription')
                        ->once()
                        ->with($stripeCustId, $stripePriceId)
                        ->andThrow(new \Exception('Stripe Subscription Error'));

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/create-subscription.json.php';
    $expectedStatus = 'stripe-error';
    $expectedMessage = 'Stripe Subscription Error';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenStripeCustomerNoSubSuccess() {
    $stripePriceId = 'STRIPE-TEST-PRICE-ID';
    $price = (object) [
      'id' => $stripePriceId
    ];
    $pyangeloPrice = [
      'stripe_price_id' => $stripePriceId
    ];
    $personId = 101;
    $stripeCustId = 'STRIPE-TEST-CUST-ID';
    $person = [
      'person_id' => $personId,
      'email' => 'joel@geelongfc.com.au',
      'given_name' => 'Joel',
      'family_name' => 'Selwood',
      'country_code' => 'AU',
      'stripe_customer_id' => $stripeCustId,
    ];
    $customer = (object) [
      'id' => $stripeCustId
    ];
    $clientSecret = 'TOP-SECRET';
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
      'customer' => $stripeCustId,
      'price_id' => $stripePriceId,
      'latest_invoice' => $latestInvoice,
      'client_secret' => $clientSecret,
      'start_date' => '2021-07-22:17:09:00',
      'status' => 'incomplete',
      'percent_off' => 0
    ];
    $this->request->post['priceId'] = $stripePriceId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->stripeWrapper->shouldReceive('retrievePrice')
                        ->once()
                        ->with($stripePriceId)
                        ->andReturn($price);
    $this->stripeRepository->shouldReceive('getStripePriceById')
                           ->once()
                           ->with($stripePriceId)
                           ->andReturn($pyangeloPrice);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('stripeCustomerId')->once()->with()->andReturn(NULL);
    $this->stripeWrapper->shouldReceive('createCustomer')
                        ->once()
                        ->with(
                          $person['email'],
                          $person['given_name'] . ' ' . $person['family_name']
                        )
                        ->andReturn($customer);
    $this->stripeRepository->shouldReceive('updateStripeCustomerId')
                           ->once()
                           ->with($person['person_id'], $stripeCustId)
                           ->andReturn(1);
    $this->stripeRepository->shouldReceive('getIncompleteSubscription')
                           ->once()
                           ->with($person['person_id'], $stripePriceId)
                           ->andReturn(NULL);
    $this->stripeWrapper->shouldReceive('createSubscription')
                        ->once()
                        ->with($stripeCustId, $stripePriceId)
                        ->andReturn($testSubscription);
    $this->stripeRepository->shouldReceive('insertSubscription')
                           ->once()
                           ->with(
                             $testSubscription->id,
                             $personId,
                             $testSubscription->current_period_start,
                             $testSubscription->current_period_end,
                             $testSubscription->customer,
                             $stripePriceId,
                             $clientSecret,
                             $testSubscription->start_date,
                             $testSubscription->status,
                             0
                           )
                           ->andReturn(1);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/create-subscription.json.php';
    $expectedStatus = 'success';
    $expectedMessage = 'Subscription created';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($stripeCustId, $responseVars['customerId']);
    $this->assertSame($person['given_name'] . ' ' . $person['family_name'], $responseVars['customerName']);
    $this->assertSame($testSubscriptionId, $responseVars['subscriptionId']);
    $this->assertSame($stripePriceId, $responseVars['priceId']);
    $this->assertSame($clientSecret, $responseVars['clientSecret']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenCustomerNoSubSuccess() {
    $stripePriceId = 'STRIPE-TEST-PRICE-ID';
    $price = (object) [
      'id' => $stripePriceId
    ];
    $pyangeloPrice = [
      'stripe_price_id' => $stripePriceId
    ];
    $personId = 101;
    $stripeCustId = 'STRIPE-TEST-CUST-ID';
    $person = [
      'person_id' => $personId,
      'email' => 'joel@geelongfc.com.au',
      'given_name' => 'Joel',
      'family_name' => 'Selwood',
      'country_code' => 'AU',
      'stripe_customer_id' => $stripeCustId,
    ];
    $clientSecret = 'TOP-SECRET';
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
      'customer' => $stripeCustId,
      'price_id' => $stripePriceId,
      'latest_invoice' => $latestInvoice,
      'client_secret' => $clientSecret,
      'start_date' => '2021-07-22:17:09:00',
      'status' => 'incomplete',
      'percent_off' => 0
    ];
    $this->request->post['priceId'] = $stripePriceId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->stripeWrapper->shouldReceive('retrievePrice')
                        ->once()
                        ->with($stripePriceId)
                        ->andReturn($price);
    $this->stripeRepository->shouldReceive('getStripePriceById')
                           ->once()
                           ->with($stripePriceId)
                           ->andReturn($pyangeloPrice);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('stripeCustomerId')->twice()->with()->andReturn($stripeCustId);
    $this->stripeRepository->shouldReceive('getIncompleteSubscription')
                           ->once()
                           ->with($person['person_id'], $stripePriceId)
                           ->andReturn(NULL);
    $this->stripeWrapper->shouldReceive('createSubscription')
                        ->once()
                        ->with($stripeCustId, $stripePriceId)
                        ->andReturn($testSubscription);
    $this->stripeRepository->shouldReceive('insertSubscription')
                           ->once()
                           ->with(
                             $testSubscription->id,
                             $personId,
                             $testSubscription->current_period_start,
                             $testSubscription->current_period_end,
                             $testSubscription->customer,
                             $stripePriceId,
                             $clientSecret,
                             $testSubscription->start_date,
                             $testSubscription->status,
                             0
                           )
                           ->andReturn(1);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/create-subscription.json.php';
    $expectedStatus = 'success';
    $expectedMessage = 'Subscription created';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($stripeCustId, $responseVars['customerId']);
    $this->assertSame($person['given_name'] . ' ' . $person['family_name'], $responseVars['customerName']);
    $this->assertSame($testSubscriptionId, $responseVars['subscriptionId']);
    $this->assertSame($stripePriceId, $responseVars['priceId']);
    $this->assertSame($clientSecret, $responseVars['clientSecret']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenCustomerAndSubSuccess() {
    $stripePriceId = 'STRIPE-TEST-PRICE-ID';
    $price = (object) [
      'id' => $stripePriceId
    ];
    $pyangeloPrice = [
      'stripe_price_id' => $stripePriceId
    ];
    $personId = 101;
    $stripeCustId = 'STRIPE-TEST-CUST-ID';
    $person = [
      'person_id' => $personId,
      'email' => 'joel@geelongfc.com.au',
      'given_name' => 'Joel',
      'family_name' => 'Selwood',
      'country_code' => 'AU',
      'stripe_customer_id' => $stripeCustId,
    ];
    $clientSecret = 'TOP-SECRET';
    $testSubscriptionId = 'TEST-SUBSCRIPTION';
    $testSubscription = [
      'subscription_id' => $testSubscriptionId,
      'current_period_start' => '2021-07-22 17:08:30',
      'current_period_end' => '2021-08-22 17:08:30',
      'stripe_customer_id' => $stripeCustId,
      'price_id' => $stripePriceId,
      'stripe_client_secret' => $clientSecret,
      'start_date' => '2021-07-22:17:09:00',
      'status' => 'incomplete',
      'percent_off' => 0
    ];
    $this->request->post['priceId'] = $stripePriceId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->stripeWrapper->shouldReceive('retrievePrice')
                        ->once()
                        ->with($stripePriceId)
                        ->andReturn($price);
    $this->stripeRepository->shouldReceive('getStripePriceById')
                           ->once()
                           ->with($stripePriceId)
                           ->andReturn($pyangeloPrice);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('stripeCustomerId')->twice()->with()->andReturn($stripeCustId);
    $this->stripeRepository->shouldReceive('getIncompleteSubscription')
                           ->once()
                           ->with($person['person_id'], $stripePriceId)
                           ->andReturn($testSubscription);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/create-subscription.json.php';
    $expectedStatus = 'success';
    $expectedMessage = 'Subscription created';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($stripeCustId, $responseVars['customerId']);
    $this->assertSame($person['given_name'] . ' ' . $person['family_name'], $responseVars['customerName']);
    $this->assertSame($testSubscriptionId, $responseVars['subscriptionId']);
    $this->assertSame($stripePriceId, $responseVars['priceId']);
    $this->assertSame($clientSecret, $responseVars['clientSecret']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }
}
?>
