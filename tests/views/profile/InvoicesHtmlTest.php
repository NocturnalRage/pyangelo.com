<?php
namespace Tests\views\profile;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class InvoicesHtmlTest extends BasicViewHtmlTestCase {
  public function tearDown(): void {
    \Mockery::close();
  }

  public function testBasicViewWithNoPayments() {
    $payments = [];
    $response = new Response('views');
    $response->setView('profile/invoices.html.php');
    $response->setVars(array(
      'pageTitle' => 'Invoices',
      'metaDescription' => "Your PyAngelo payments.",
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'activeLink' => 'invoices',
      'payments' => $payments
    ));
    $output = $response->requireView();
    $expect = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<form id="logout-form" action="/logout" method="POST" style="display: none;">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/invoices" class="list-group-item active"><i class="fa fa-file-text-o fa-fw"></i> Invoices</a>';
    $this->assertStringContainsString($expect, $output);
    $expect = "<h1>You Haven't Made Any Payments</h1>";
    $this->assertStringContainsString($expect, $output);
  }

  public function testBasicViewWithPayments() {
    $payments = [
      [
        'paid_at_formatted' => '11th January 2017',
        'display_amount' => '99.95',
        'currency_symbol' => '$',
        'currency_code' => 'USD',
        'payment_type_name' => 'Payment'
      ],
      [
        'paid_at_formatted' => '11th January 2016',
        'display_amount' => '99.95',
        'currency_symbol' => '$',
        'currency_code' => 'USD',
        'payment_type_name' => 'Payment'
      ]
    ];
    $subscription = [];
    $numberFormatter = \Mockery::mock('\NumberFormatter');
    $numberFormatter->shouldReceive('formatCurrency')->times(2)->with(99.95, 'USD')->andReturn('$99.95');
    $response = new Response('views');
    $response->setView('profile/invoices.html.php');
    $response->setVars(array(
      'pageTitle' => 'Invoices',
      'metaDescription' => "Your PyAngelo payments.",
      'activeLink' => 'invoices',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'payments' => $payments,
      'numberFormatter' => $numberFormatter
    ));
    $output = $response->requireView();
    $expect = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<form id="logout-form" action="/logout" method="POST" style="display: none;">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/invoices" class="list-group-item active"><i class="fa fa-file-text-o fa-fw"></i> Invoices</a>';
    $this->assertStringContainsString($expect, $output);
    $expect = "<h1>Payment History</h1>";
    $this->assertStringContainsString($expect, $output);
    $expect = "11th January 2016";
    $this->assertStringContainsString($expect, $output);
    $expect = "$99.95";
    $this->assertStringContainsString($expect, $output);
  }
}
?>
