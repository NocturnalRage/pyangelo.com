<?php
namespace tests\src\PyAngelo\Controllers\Membership;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Membership\PremiumWelcomeController;

class PremiumWelcomeControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->stripeWrapper = Mockery::mock('Framework\Billing\StripeWrapper');
    $this->controller = new PremiumWelcomeController(
      $this->request,
      $this->response,
      $this->auth,
      $this->stripeWrapper
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Membership\PremiumWelcomeController');
  }

  public function testPaymentSucceeded() {
    $pi = (object) [
      'status' => 'succeeded'
    ];
    $testPaymentIntentParam = 'testing payment intent';
    $this->request->get['payment_intent'] =  $testPaymentIntentParam;
    $this->stripeWrapper->shouldReceive('retrievePaymentIntent')->once()->with($testPaymentIntentParam)->andReturn($pi);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/premium-member-welcome.html.php';
    $expectedPageTitle = "You've Joined as a PyAngelo Premium Member";
    $expectedMetaDescription = "You have now joined as a PyAngelo premium member and have full access to our website and coding tutorials.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  public function testPaymentProcessing() {
    $pi = (object) [
      'status' => 'processing'
    ];
    $testPaymentIntentParam = 'testing payment intent';
    $this->request->get['payment_intent'] =  $testPaymentIntentParam;
    $this->stripeWrapper->shouldReceive('retrievePaymentIntent')->once()->with($testPaymentIntentParam)->andReturn($pi);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/payment-currently-processing.html.php';
    $expectedPageTitle = "Your Payment is Currently Processing";
    $expectedMetaDescription = "Your payment is still being processed by the authorities. We will update you when your payment has been received.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  public function testRequiresPaymentMethod() {
    $pi = (object) [
      'status' => 'requires_payment_method'
    ];
    $testPaymentIntentParam = 'testing payment intent';
    $this->request->get['payment_intent'] =  $testPaymentIntentParam;
    $this->stripeWrapper->shouldReceive('retrievePaymentIntent')->once()->with($testPaymentIntentParam)->andReturn($pi);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /choose-plan'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('Payment failed. Please try another payment method.', $_SESSION['flash']['message']);
  }

  public function testUnknownError() {
    $pi = (object) [
      'status' => 'some_weird_status'
    ];
    $testPaymentIntentParam = 'testing payment intent';
    $this->request->get['payment_intent'] =  $testPaymentIntentParam;
    $this->stripeWrapper->shouldReceive('retrievePaymentIntent')->once()->with($testPaymentIntentParam)->andReturn($pi);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /choose-plan'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('Something went wrong. Please try again.', $_SESSION['flash']['message']);
  }

  public function testWelcomeController() {
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/premium-member-welcome.html.php';
    $expectedPageTitle = "You've Joined as a PyAngelo Premium Member";
    $expectedMetaDescription = "You have now joined as a PyAngelo premium member and have full access to our website and coding tutorials.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
