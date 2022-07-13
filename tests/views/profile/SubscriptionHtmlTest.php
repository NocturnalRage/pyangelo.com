<?php
namespace Tests\views\profile;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class SubscriptionHtmlTest extends BasicViewHtmlTest {
  public function tearDown(): void {
    \Mockery::close();
  }

  public function testBasicViewWithActiveMonthlySubscription() {
    $subscription = [
      'currency_symbol' => '$',
      'currency_code' => 'USD',
      'price_in_cents' => 999,
      'stripe_divisor' => 100,
      'percent_off' => 0,
      'billing_period' => 'month',
      'premiumMemberSince' => '1 week ago',
      'nextPaymentDate' => '3 weeks from now',
      'cancel_at_period_end' => 0
    ];
    $numberFormatter = \Mockery::mock('\NumberFormatter');
    $numberFormatter->shouldReceive('formatCurrency')->times(1)->with(9.99, 'USD')->andReturn('$9.99');
    $response = new Response('views');
    $response->setView('profile/subscription.html.php');
    $response->setVars(array(
      'pageTitle' => 'Subscription Information',
      'metaDescription' => "Information about your PyAngelo subscription.",
      'activeLink' => 'subscription',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'subscription' => $subscription,
      'numberFormatter' => $numberFormatter
    ));
    $output = $response->requireView();
    $expect = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<form id="logout-form" action="/logout" method="POST" style="display: none;">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/subscription" class="list-group-item active"><i class="fa fa-shopping-bag fa-fw"></i> Subscription</a>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h1>Cancel Your Subscription</h1>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<button type="submit" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to cancel your membership?\')">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<form action="/toggle-cancel-subscription" method="POST">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="hidden" name="crsfToken" value="dummy-crsf-token" />';
    $this->assertStringContainsString($expect, $output);
  }

  public function testBasicViewWithCanceledAtMonthlySubscription() {
    $subscription = [
      'currency_symbol' => '$',
      'currency_code' => 'USD',
      'price_in_cents' => 999,
      'stripe_divisor' => 100,
      'percent_off' => 0,
      'billing_period' => 'month',
      'premiumMemberSince' => '1 week ago',
      'nextPaymentDate' => '3 weeks from now',
      'cancel_at_period_end' => 1
    ];
    $numberFormatter = \Mockery::mock('\NumberFormatter');
    $numberFormatter->shouldReceive('formatCurrency')->times(1)->with(9.99, 'USD')->andReturn('$9.99');
    $response = new Response('views');
    $response->setView('profile/subscription.html.php');
    $response->setVars(array(
      'pageTitle' => 'Subscription Information',
      'metaDescription' => "Information about your PyAngelo subscription.",
      'activeLink' => 'subscription',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'subscription' => $subscription,
      'numberFormatter' => $numberFormatter
    ));
    $output = $response->requireView();
    $expect = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<form id="logout-form" action="/logout" method="POST" style="display: none;">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/subscription" class="list-group-item active"><i class="fa fa-shopping-bag fa-fw"></i> Subscription</a>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h1>Upgrade To Our Yearly Plan</h1>';
    $this->assertStringNotContainsString($expect, $output);
    $expect = '<h1>Cancel Your Subscription</h1>';
    $this->assertStringNotContainsString($expect, $output);
    $expect = '<h1>Resume Your Subscription</h1>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<form action="/toggle-cancel-subscription" method="POST">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<i class="fa fa-refresh" aria-hidden="true"></i> Resume Your Subscription';
    $this->assertStringContainsString($expect, $output);
  }

  public function testBasicViewWithNoSubscription() {
    $numberFormatter = \Mockery::mock('\NumberFormatter');
    $response = new Response('views');
    $response->setView('profile/subscription.html.php');
    $response->setVars(array(
      'pageTitle' => 'Subscription Information',
      'metaDescription' => "Information about your PyAngelo subscription.",
      'activeLink' => 'subscription',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'numberFormatter' => $numberFormatter
    ));
    $output = $response->requireView();
    $expect = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<form id="logout-form" action="/logout" method="POST" style="display: none;">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/subscription" class="list-group-item active"><i class="fa fa-shopping-bag fa-fw"></i> Subscription</a>';
    $this->assertStringContainsString($expect, $output);
    $expect = "<h1>You Don't Have an Active Subscription</h1>";
    $this->assertStringContainsString($expect, $output);
  }
}
?>
