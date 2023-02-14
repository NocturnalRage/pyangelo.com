<?php
namespace tests\src\PyAngelo\Controllers\Membership;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Membership\SubscriptionPaymentFormController;

class SubscriptionPaymentFormControllerTest extends TestCase {
  protected $stripeWrapper;
  protected $stripeRepository;
  protected $numberFormatter;
  protected $request;
  protected $response;
  protected $auth;
  protected $controller;

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

  public function testWhenFormSuccessfullyLoaded() {
    $this->request->env['STRIPE_PUBLISHABLE_KEY'] = 'STRIPE_PUBLISHABLE_KEY';
    $priceId = 'TEST-PRICE-ID';
    $this->request->get['priceId'] = $priceId;
    $price = (object) [
      'id' => $priceId
    ];
    $pyangeloPrice = [
      'stripe_price_id' => $priceId
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->stripeWrapper->shouldReceive('retrievePrice')->once()->with($priceId)->andReturn($price);
    $this->stripeRepository->shouldReceive('getStripePriceById')->once()->with($priceId)->andReturn($pyangeloPrice);
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
