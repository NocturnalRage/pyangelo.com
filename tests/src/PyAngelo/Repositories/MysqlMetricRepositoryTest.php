<?php
namespace Tests\PyAngelo\Repositories;

use PHPUnit\Framework\TestCase;
use PyAngelo\Repositories\MysqlMetricRepository;
use Tests\Factory\TestData;
use DateTime;
use DateInterval;
use DatePeriod;

class MysqlMetricRepositoryTest extends TestCase {
  protected $dbh;
  protected $stripeRepository;
  protected $testData;

  public function setUp(): void {
    $dotenv  = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../../../', '.env.test');
    $dotenv->load();
    $this->dbh = new \Mysqli(
      $_ENV['DB_HOST'],
      $_ENV['DB_USERNAME'],
      $_ENV['DB_PASSWORD'],
      $_ENV['DB_DATABASE']
    );
    $this->metricRepository = new MysqlMetricRepository($this->dbh);
    $this->testData = new TestData($this->dbh);
  }

  public function tearDown(): void {
    $this->dbh->close();
  }

  public function testGetSubscriberGrowth() {
    $this->testData->createSubscribers();
    $growth = $this->metricRepository->getSubscriberGrowthByMonth();
    $expectedGrowth = [
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
    $this->assertEquals($expectedGrowth, $growth);
  }

  public function testGetSubscriberPayments() {
    $this->testData->createSubscriberPayments();
    $payments = $this->metricRepository->getSubscriberPaymentsByMonth();
    $expectedPayments = [
      [
        'startmonth' => 'Jan 2017',
        'ordermonth' => '201701',
        'pyangelo' => 6.0000,
        'stripe' => 0.1000,
        'tax' => 0.900
      ]
    ];
    $this->assertEquals($expectedPayments, $payments);
  }

  public function testGetPremiumMemberCount() {
    $this->testData->createSubscribers();
    $memberCount = $this->metricRepository->getPremiumMemberCountByMonth();
    $start    = new DateTime('11 months ago');
    // So you don't skip February if today is day the 29th, 30th, or 31st
    $start->modify('first day of this month');
    $end      = new DateTime();
    $interval = new DateInterval('P1M');
    $period   = new DatePeriod($start, $interval, $end);
    $expectedMemberCount = [];
    foreach ($period as $dt) {
      if ($dt->format('M Y') == 'Dec 2016') {
        $members = 2;
      }
      else if ($dt->format('M Y') == 'Jan 2017') {
        $members = 1;
      }
      else {
        $members = 0;
      }
      $expectedMemberCount[] = [
        'month' => $dt->format('M Y'),
        'premium_member_count' => $members
      ];
    }
    // December 2 January 1
    $this->assertEquals($expectedMemberCount, $memberCount);
  }

  public function testGetPremiumMemberCountByPlan() {
    $this->testData->createSubscribers();
    $plans = $this->metricRepository->getPremiumMemberCountByPlan();
    $expectedPlans = [
      [
        'display_plan_name' => 'Monthly',
        'count' => 1
      ]
    ];
    $this->assertEquals($expectedPlans, $plans);
  }

  public function testGetMemberCountByMonth() {
    $this->testData->createPerson(1, 'fastfred@hotmail.com');
    $month = date('M Y');
    $cym = date('Ym');
    $membersMonthly = $this->metricRepository->getMemberCountByMonth();
    $expectedMembersMonthly = [
      [
        'cym' => $cym,
        'month' => $month,
        'count' => 1
      ]
    ];
    $this->assertEquals($expectedMembersMonthly, $membersMonthly);
  }

  public function testGetMemberCountByDay() {
    $this->testData->createPerson(1, 'fastfred@hotmail.com');
    $day = date('Y-m-d');
    $membersDaily = $this->metricRepository->getMemberCountByDay();
    $expectedMembersDaily = [
      [
        'created_at' => $day,
        'count' => 1
      ]
    ];
    $this->assertEquals($expectedMembersDaily, $membersDaily);
  }
}

