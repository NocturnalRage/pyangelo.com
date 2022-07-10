<?php
namespace tests\src\PyAngelo\Controllers\Membership;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Membership\ChoosePlanController;

class ChoosePlanControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->stripeRepository = Mockery::mock('PyAngelo\Repositories\StripeRepository');
    $this->countryRepository = Mockery::mock('PyAngelo\Repositories\CountryRepository');
    $this->countryDetector = Mockery::mock('PyAngelo\Utilities\CountryDetector');
    $this->numberFormatter = Mockery::mock('\NumberFormatter');
    $this->controller = new ChoosePlanController(
      $this->request,
      $this->response,
      $this->auth,
      $this->stripeRepository,
      $this->countryRepository,
      $this->countryDetector,
      $this->numberFormatter
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Membership\ChoosePlanController');
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/choose-plan-not-logged-in.html.php';
    $expectedPageTitle = 'Get Full Access to all PyAngelo Tutorials';
    $expectedMetaDescription = 'A monthly subscription will give you full access to every tutorial on the PyAngelo website.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  public function testWhenActiveSubscription() {
    $countryCode = 'US';
    $currencyCode = 'USD';
    $currency = [
      'currency_code' => $currencyCode
    ];
    $person = [
      'person_id' => 1,
      'email' => 'joel@geelongfc.com.au',
      'given_name' => 'Joel',
      'family_name' => 'Selwood',
      'country_code' => 'US'
    ];
    $testStripePriceId = "test-price";
    $membershipPrices = [
      [
         'product_name' => 'PyAngelo Premium Membership',
         'stripe_price_id' => $testStripePriceId,
         'price_in_cents' => 995
      ]
    ];
    $this->auth->shouldReceive('loggedIn')->twice()->with()->andReturn(true);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->stripeRepository->shouldReceive('getMembershipPrices')->once()->with($currencyCode)->andReturn($membershipPrices);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(true);
    $this->countryRepository->shouldReceive('getCurrencyFromCountryCode')->once()->with($countryCode)->andReturn($currency);

    $this->request->env['STRIPE_PUBLISHABLE_KEY'] = 'TEST-STRIPE-KEY';
    $this->request->server['REQUEST_URI'] = '/choose-plan';

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /subscription'));
    $expectedFlashMessage = "You already have full access with your current subscription!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testWhenNotActiveSubscription() {
    $this->request->env['STRIPE_PUBLISHABLE_KEY'] = 'TEST-STRIPE-KEY';
    $countryCode = 'US';
    $currencyCode = 'USD';
    $currency = [
      'currency_code' => $currencyCode
    ];
    $testStripePriceId = "test-price";
    $membershipPrices = [
      [
         'product_name' => 'PyAngelo Premium Membership',
         'stripe_price_id' => $testStripePriceId,
         'price_in_cents' => 995
      ]
    ];
    $this->request->server['REQUEST_URI'] = '/choose-plan';
    $testCustomerId = 'TEST-CUSTOMER';
    $clientSecret = 'TOP-SECRET';
    $person = [
      'person_id' => 1,
      'email' => 'joel@geelongfc.com.au',
      'given_name' => 'Joel',
      'family_name' => 'Selwood',
      'country_code' => 'US'
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
    $this->auth->shouldReceive('loggedIn')->twice()->with()->andReturn(true);
    $this->countryRepository->shouldReceive('getCurrencyFromCountryCode')->once()->with($countryCode)->andReturn($currency);
    $this->stripeRepository->shouldReceive('getMembershipPrices')->once()->with($currencyCode)->andReturn($membershipPrices);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();

    $this->request->post["priceId"] = $testStripePriceId;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/choose-plan.html.php';
    $expectedPageTitle = 'Get Full Access to all PyAngelo Tutorials';
    $expectedMetaDescription = 'A monthly subscription will give you full access to every tutorial on the PyAngelo website.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
