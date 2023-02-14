<?php
namespace Tests\views\membership;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class SubscriptionPaymentFormHtmlTest extends BasicViewHtmlTestCase {
  public function tearDown(): void {
    \Mockery::close();
  }

  public function testBasicViewForPaymentForm() {
    $productName = 'Full Access';
    $productDescription = 'Full Access Description';
    $pyangeloPrice = [
      'product_name' => $productName,
      'product_description' => $productDescription,
      'currency_code' => 'AUD',
      'currency_description' => 'Australian Dollars',
      'stripe_divisor' => 100
    ];
    $testPriceId = 'TEST-PRICE-ID';
    $stripePrice = (object) [
      'id' => $testPriceId,
      'unit_amount' => 995
    ];
    $numberFormatter = \Mockery::mock('\NumberFormatter');
    $numberFormatter->shouldReceive('formatCurrency')->times(1)->with(9.95, 'AUD')->andReturn('$9.95');
    $response = new Response('views');
    $response->setView('membership/subscription-payment-form.html.php');
    $response->setVars(array(
      'pageTitle' => 'Become a PyAngelo Premium Member',
      'metaDescription' => "Sign up to a subscription to become a premium member.",
      'activeLink' => 'Premium Membership',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'pyangeloPrice' => $pyangeloPrice,
      'stripePrice' => $stripePrice,
      'stripePublishableKey' => 'STRIPE-KEY',
      'clientSecret' => 'CLIENT-SECRET',
      'numberFormatter' => $numberFormatter
    ));
    $output = $response->requireView();
    $expect = '<script src="https://js.stripe.com/v3/"></script>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<form id="payment-form" data-crsf-token="dummy-crsf-token" data-price-id="' . $testPriceId . '" data-stripe-publishable-key="STRIPE-KEY">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<div id="payment-element" class="add-bottom">';
    $this->assertStringContainsString($expect, $output);
    $expect = 'Full Access Desc';
    $this->assertStringContainsString($expect, $output);
    $expect = "$9.95 per Month";
    $this->assertStringContainsString($expect, $output);
    $expect = 'Common Questions';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
