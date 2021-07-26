<?php
namespace Tests\views\admin;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class UserHtmlTest extends BasicViewHtmlTest {

  public function tearDown(): void {
    \Mockery::close();
  }

  public function testBasicViewShowingUser() {
    $email = 'fastfred@hotmail.com';
    $displayName = 'Fast Fred';
    $person = [
      'person_id' => 100,
      'given_name' => 'Fred',
      'display_name' => $displayName,
      'country_name' => 'Australia',
      'premium_status_boolean' => 1,
      'email' => 'fastfred@hotmail.com',
      'created_at' => '2021-07-17 13:15:30'
    ];
    $payments = [
      [
        'paid_at_formatted' => '2017-01-10',
        'currency_symbol' => '$',
        'charge_id' => 'FAKE_CHARGE_ID',
        'currency_code' => 'AUD',
        'display_amount' => '10.00',
        'total_amount_in_cents' => '1000',
        'payment_type_name' => 'Payment',
      ]
    ];
    $avatar = \Mockery::mock('Framework\Presentation\Gravatar');
    $avatar->shouldReceive('getAvatarUrl')->once()->with($email)->andReturn('avatar');
    $numberFormatter = \Mockery::mock('\NumberFormatter');
    $numberFormatter->shouldReceive('formatCurrency')->times(1)->with(10.00, 'AUD')->andReturn('$10.00');
    $response = new Response('views');
    $response->setView('admin/user.html.php');
    $response->setVars(array(
      'pageTitle' => "PyAngelo Admin",
      'metaDescription' => "Update the PyAngelo website through the administration pages.",
      'activeLink' => 'Admin',
      'personInfo' => $this->setPersonInfoAdmin(),
      'person' => $person,
      'avatar' => $avatar,
      'payments' => $payments,
      'numberFormatter' => $numberFormatter
    ));
    $output = $response->requireView();
    $this->assertStringContainsString($displayName, $output);
    $expected = '<img class="media-object featuredThumbnail" src="avatar" alt="' . $displayName . '" />';
    $this->assertStringContainsString($expected, $output);
    $expected = '<h4 class="media-heading">Fast Fred <small><i>Premium Member</i></small></h4>';
    $this->assertStringContainsString($expected, $output);
    $expected = '<p><strong>Email: </strong>fastfred@hotmail.com</p>';
    $this->assertStringContainsString($expected, $output);
    $expected = '<p><strong>Country: </strong>Australia</p>';
    $this->assertStringContainsString($expected, $output);
    $expected = 'Payment History';
    $this->assertStringContainsString($expected, $output);
    $expected = '$10.00';
    $this->assertStringContainsString($expected, $output);
  }
}
?>
