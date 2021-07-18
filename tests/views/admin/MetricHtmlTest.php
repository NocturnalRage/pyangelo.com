<?php
namespace Tests\views\admin;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class MetricHtmlTest extends BasicViewHtmlTest {
  public function tearDown(): void {
    \Mockery::close();
  }

  public function testBasicView() {
    $keyMetrics = [
      [
        'total_members' => 1000,
        'premium_members' => 100,
        'past_due' => 2
      ]
    ];
    $subscriberGrowth = [
      [
        'startmonth' => 'Dec 2016',
        'ordermonth' => '201612',
        'subscribed' => 2,
        'cancelled' => 2,
        'net' => 0
      ],
      [
        'startmonth' => 'Jan 2017',
        'ordermonth' => '201701',
        'subscribed' => 1,
        'cancelled' => 0,
        'net' => 1
      ]
    ];
    $subscriberPayments = [
      [
        'startmonth' => 'Jan 2017',
        'ordermonth' => '201701',
        'pyangelo' => 600,
        'stripe' => 10,
        'tax' => 90
      ]
    ];
    $premiumMembers = [
      [
        'month' => 'Jan 2017',
        'premium_member_count' => 1
      ]
    ];
    $plans = [
      [
        'display_plan_name' => 'Monthly',
        'count' => 1
      ]
    ];
    $premiumCountries = [
      [
        'country_name' => 'Australia',
        'count' => 1
      ]
    ];
    $membersMonthly = [
      [
        'cym' => '201701',
        'month' => 'Jan 2017',
        'count' => 1
      ]
    ];
    $membersDaily = [
      [
        'created_at' => '2017-01-01',
        'count' => 1
      ]
    ];
    $memberCountries = [
      [
        'country_name' => 'Australia',
        'count' => 1
      ]
    ];
    $person = [
      'person_id' => 2,
      'given_name' => 'Jeff'
    ];
    $response = new Response('views');
    $response->setView('admin/metrics.html.php');
    $response->setVars(array(
      'pageTitle' => "PyAngelo Admin",
      'metaDescription' => "Update the PyAngelo website through the administration pages.",
      'activeLink' => 'Admin',
      'personInfo' => $this->setPersonInfoAdmin(),
      'keyMetrics' => $keyMetrics,
      'subscriberGrowth' => $subscriberGrowth,
      'subscriberPayments' => $subscriberPayments,
      'premiumMembers' => $premiumMembers,
      'plans' => $plans,
      'premiumCountries' => $premiumCountries,
      'membersMonthly' => $membersMonthly,
      'membersDaily' => $membersDaily,
      'memberCountries' => $memberCountries
    ));
    $output = $response->requireView();
    $adminMenu = '<li><a href="/admin"><i class="fa fa-lock fa-fw"></i> Admin</a></li>';
    $this->assertStringContainsString($adminMenu, $output);
    $expect = 'PyAngelo Metrics';
    $this->assertStringContainsString($expect, $output);
    $expect = 'Paying Member Growth';
    $this->assertStringContainsString($expect, $output);
    $expect = 'Subscription Payments';
    $this->assertStringContainsString($expect, $output);
    $expect = 'Paying Members Last 12 Months';
    $this->assertStringContainsString($expect, $output);
    $expect = 'Paying Members By Plan';
    $this->assertStringContainsString($expect, $output);
    $expect = 'Paying Members Per Country';
    $this->assertStringContainsString($expect, $output);
    $expect = 'Free Members Per Month';
    $this->assertStringContainsString($expect, $output);
    $expect = 'Free Members Per Day';
    $this->assertStringContainsString($expect, $output);
    $expect = 'Members Per Country';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
