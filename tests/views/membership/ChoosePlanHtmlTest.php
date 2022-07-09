<?php
namespace Tests\views\membership;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class ChoosePlanHtmlTest extends BasicViewHtmlTest {
  public function tearDown(): void {
    \Mockery::close();
  }

  public function testBasicLoginViewWhenActiveSubscription() {
    $currency = [
      'stripe_divisor' => 100,
      'currency_code' => 'USD',
      'currency_description' => 'US Dollars'
    ];
    $membershipPrices = [
       [
          'product_id' => 'STRIPE_PROD_ID',
          'product_name' => 'Full Access',
          'product_description' => 'Full Access Desc',
          'stripe_price_id' => 'STRIPE_PRICE_ID_FULL',
          'currency_code' => 'USD',
          'price_in_cents' => 995,
          'billing_period' => 'Monthly'
       ],
       [
          'product_id' => 'STRIPE_PROD_ID_2',
          'product_name' => 'Full Access Plus',
          'product_description' => 'Full Access Plus Desc',
          'stripe_price_id' => 'STRIPE_PRICE_ID_PLUS',
          'currency_code' => 'USD',
          'price_in_cents' => 1495,
          'billing_period' => 'Monthly'
       ]
    ];
    $numberFormatter = \Mockery::mock('\NumberFormatter');
    $numberFormatter->shouldReceive('formatCurrency')->times(1)->with(9.95, 'USD')->andReturn('$9.95');
    $numberFormatter->shouldReceive('formatCurrency')->times(1)->with(14.95, 'USD')->andReturn('$14.95');
    $response = new Response('views');
    $response->setView('membership/choose-plan.html.php');
    $response->setVars(array(
      'pageTitle' => 'Become a PyAngelo Premium Member',
      'metaDescription' => "Sign up to a subscription to become a premium member.",
      'activeLink' => 'Premium Membership',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'stripePublishableKey' => 'STRIPE_PUBLIC_KEY',
      'currency' => $currency,
      'hasActiveSubscription' => false,
      'membershipPrices' => $membershipPrices,
      'numberFormatter' => $numberFormatter
    ));
    $output = $response->requireView();
    $expect = 'Get Full Access to Every Tutorial';
    $this->assertStringContainsString($expect, $output);
    $expect = "Choose a Monthly Plan";
    $this->assertStringContainsString($expect, $output);
    $expect = '<h1>$9.95 per Month</h1>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/subscription-payment-form/STRIPE_PRICE_ID_FULL">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h1>$14.95 per Month</h1>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/subscription-payment-form/STRIPE_PRICE_ID_PLUS">';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
