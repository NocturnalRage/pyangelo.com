<?php
namespace tests\src\PyAngelo\Controllers\Membership;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Membership\SubscriptionPaymentFormController;

class SubscriptionPaymentFormControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->stripeWrapper = Mockery::mock('Framework\Billing\StripeWrapper');
    $this->stripeRepository = Mockery::mock('PyAngelo\Repositories\StripeRepository');
    $this->numberFormatter = Mockery::mock('\NumberFormatter');
    $this->controller = new SubscriptionPaymentFormController(
      $this->request,
      $this->response,
      $this->auth,
      $this->stripeWrapper,
      $this->stripeRepository,
      $this->numberFormatter
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Membership\SubscriptionPaymentFormController');
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $expectedHeaders = array(array('header', 'Location: /choose-plan'));
    $expectedFlashMessage = "You must be logged in to create a subscription!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testWhenActiveSubscription() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $expectedHeaders = array(array('header', 'Location: /subscription'));
    $expectedFlashMessage = "You already have full access with your current subscription!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testWhenNoPriceId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $expectedHeaders = array(array('header', 'Location: /choose-plan'));
    $expectedFlashMessage = "You must select a monthly plan!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testWhenInvalidPriceId() {
    $priceId = 'TEST-PRICE-ID';
    $this->request->get['priceId'] = $priceId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->stripeWrapper->shouldReceive('retrievePrice')->once()->with($priceId)->andThrow(new \Exception());

    $response = $this->controller->exec();
    $expectedHeaders = array(array('header', 'Location: /choose-plan'));
    $expectedFlashMessage = "You must select a monthly plan!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testWhenNoCustomerNoSubscriptionCreateFails() {
    $priceId = 'TEST-PRICE-ID';
    $price = (object) [
      'id' => $priceId
    ];
    $pyangeloPrice = [
      'stripe_price_id' => $priceId
    ];
    $stripeCustId = 'STRIPE-CUS-1';
    $personId = 1;
    $person = [
      'person_id' => $personId,
      'email' => 'joel@geelongfc.com.au',
      'given_name' => 'Joel',
      'family_name' => 'Selwood',
      'country_code' => 'US',
      'stripe_customer_id' => $stripeCustId,
    ];
    $customer = (object) [
      'id' => $stripeCustId
    ];
    $stripeErrorMessage = 'There was an error with Stripe';
    $this->request->get['priceId'] = $priceId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->stripeWrapper->shouldReceive('retrievePrice')->once()->with($priceId)->andReturn($price);
    $this->stripeRepository->shouldReceive('getStripePriceById')->once()->with($priceId)->andReturn($pyangeloPrice);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('stripeCustomerId')->once()->with()->andReturn(NULL);
    $this->stripeWrapper->shouldReceive('createCustomer')
                        ->once()
                        ->with($person["email"], $person["given_name"] . " " . $person["family_name"])
                        ->andReturn($customer);
    $this->stripeRepository->shouldReceive('updateStripeCustomerId')
                           ->once()
                           ->with($personId, $stripeCustId)
                           ->andReturn(NULL);
    $this->stripeRepository->shouldReceive('getIncompleteSubscription')
                           ->once()
                           ->with($personId, $priceId)
                           ->andReturn(NULL);
    $this->stripeWrapper->shouldReceive('createSubscription')
                        ->once()
                        ->with($stripeCustId, $priceId)
                        ->andThrow(new \Exception($stripeErrorMessage));

    $response = $this->controller->exec();
    $expectedHeaders = array(array('header', 'Location: /choose-plan'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($stripeErrorMessage, $_SESSION['flash']['message']);
  }

  public function testWhenCustomerNoSubscriptionCreateFails() {
    $priceId = 'TEST-PRICE-ID';
    $price = (object) [
      'id' => $priceId
    ];
    $pyangeloPrice = [
      'stripe_price_id' => $priceId
    ];
    $stripeCustId = 'STRIPE-CUS-1';
    $personId = 1;
    $person = [
      'person_id' => $personId,
      'email' => 'joel@geelongfc.com.au',
      'given_name' => 'Joel',
      'family_name' => 'Selwood',
      'country_code' => 'US',
      'stripe_customer_id' => $stripeCustId,
    ];
    $stripeErrorMessage = 'There was an error with Stripe';
    $this->request->get['priceId'] = $priceId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->stripeWrapper->shouldReceive('retrievePrice')->once()->with($priceId)->andReturn($price);
    $this->stripeRepository->shouldReceive('getStripePriceById')->once()->with($priceId)->andReturn($pyangeloPrice);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('stripeCustomerId')->twice()->with()->andReturn($stripeCustId);
    $this->stripeRepository->shouldReceive('getIncompleteSubscription')
                           ->once()
                           ->with($personId, $priceId)
                           ->andReturn(NULL);
    $this->stripeWrapper->shouldReceive('createSubscription')
                        ->once()
                        ->with($stripeCustId, $priceId)
                        ->andThrow(new \Exception($stripeErrorMessage));

    $response = $this->controller->exec();
    $expectedHeaders = array(array('header', 'Location: /choose-plan'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($stripeErrorMessage, $_SESSION['flash']['message']);
  }

  public function testWhenCustomerNoSubscriptionCreateSuccess() {
    $this->request->env['STRIPE_PUBLISHABLE_KEY'] = 'STRIPE_PUBLISHABLE_KEY';
    $priceId = 'TEST-PRICE-ID';
    $price = (object) [
      'id' => $priceId
    ];
    $pyangeloPrice = [
      'stripe_price_id' => $priceId
    ];
    $stripeCustId = 'STRIPE-CUS-1';
    $personId = 1;
    $person = [
      'person_id' => $personId,
      'email' => 'joel@geelongfc.com.au',
      'given_name' => 'Joel',
      'family_name' => 'Selwood',
      'country_code' => 'US',
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
      'price_id' => $priceId,
      'latest_invoice' => $latestInvoice,
      'client_secret' => $clientSecret,
      'start_date' => '2021-07-22:17:09:00',
      'status' => 'incomplete',
      'percent_off' => 0
    ];
    $stripeErrorMessage = 'There was an error with Stripe';
    $this->request->get['priceId'] = $priceId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->stripeWrapper->shouldReceive('retrievePrice')->once()->with($priceId)->andReturn($price);
    $this->stripeRepository->shouldReceive('getStripePriceById')->once()->with($priceId)->andReturn($pyangeloPrice);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('stripeCustomerId')->twice()->with()->andReturn($stripeCustId);
    $this->stripeRepository->shouldReceive('getIncompleteSubscription')
                           ->once()
                           ->with($personId, $priceId)
                           ->andReturn(NULL);
    $this->stripeWrapper->shouldReceive('createSubscription')
                        ->once()
                        ->with($stripeCustId, $priceId)
                        ->andReturn($testSubscription);
    $this->stripeRepository->shouldReceive('insertSubscription')
                           ->once()
                           ->with(
                             $testSubscription->id,
                             $personId,
                             $testSubscription->current_period_start,
                             $testSubscription->current_period_end,
                             $testSubscription->customer,
                             $priceId,
                             $clientSecret,
                             $testSubscription->start_date,
                             $testSubscription->status,
                             0
                           )
                           ->andReturn(1);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/subscription-payment-form.html.php';
    $expectedPageTitle = 'Subscribe to a Monthly Plan';
    $expectedMetaDescription = 'Enter your payment details to start a monthly subscription plan to the PyAngelo website.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  public function testWhenCustomerWithSubscription() {
    $this->request->env['STRIPE_PUBLISHABLE_KEY'] = 'STRIPE_PUBLISHABLE_KEY';
    $priceId = 'TEST-PRICE-ID';
    $price = (object) [
      'id' => $priceId
    ];
    $pyangeloPrice = [
      'stripe_price_id' => $priceId
    ];
    $stripeCustId = 'STRIPE-CUS-1';
    $personId = 1;
    $person = [
      'person_id' => $personId,
      'email' => 'joel@geelongfc.com.au',
      'given_name' => 'Joel',
      'family_name' => 'Selwood',
      'country_code' => 'US',
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
    $testSubscription = [
      'subscription_id' => $testSubscriptionId,
      'person_id' => $personId,
      'cancel_at_period_end' => NULL,
      'cancel_at' => NULL,
      'current_period_start' => '2021-07-22 17:08:30',
      'current_period_end' => '2021-08-22 17:08:30',
      'stripe_customer_id' => $stripeCustId,
      'stripe_price_id' => $priceId,
      'stripe_client_secret' => $clientSecret,
      'start_date' => '2021-07-22:17:09:00',
      'status' => 'incomplete',
      'percent_off' => 0,
      'created_at' => '2021-07-22:17:09:00',
      'updated_at' => '2021-07-22:17:09:00'
    ];
    $stripeErrorMessage = 'There was an error with Stripe';
    $this->request->get['priceId'] = $priceId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->stripeWrapper->shouldReceive('retrievePrice')->once()->with($priceId)->andReturn($price);
    $this->stripeRepository->shouldReceive('getStripePriceById')->once()->with($priceId)->andReturn($pyangeloPrice);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('stripeCustomerId')->twice()->with()->andReturn($stripeCustId);
    $this->stripeRepository->shouldReceive('getIncompleteSubscription')
                           ->once()
                           ->with($personId, $priceId)
                           ->andReturn($testSubscription);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/subscription-payment-form.html.php';
    $expectedPageTitle = 'Subscribe to a Monthly Plan';
    $expectedMetaDescription = 'Enter your payment details to start a monthly subscription plan to the PyAngelo website.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
