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
  protected $metricRepository;
  protected $testData;
  protected $personId;
  protected $priceId;

  public function setUp(): void {
    $dotenv  = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../../../', '.env.test');
    $dotenv->load();
    $this->dbh = new \Mysqli(
      $_ENV['DB_HOST'],
      $_ENV['DB_USERNAME'],
      $_ENV['DB_PASSWORD'],
      $_ENV['DB_DATABASE']
    );
    $this->dbh->begin_transaction();
    $this->metricRepository = new MysqlMetricRepository($this->dbh);
    $this->testData = new TestData($this->dbh);
    $this->personId = 1;
    $this->priceId = 'test-price-id';
    $this->testData->createCurrency('USD', 'United States Dollar', '$', 100);
    $this->testData->createCountry('US', 'United States', 'USD');
    $this->testData->createPerson($this->personId, 'coder@hotmail.com');
    $this->testData->createPrice($this->priceId, 'Monthly');
  }

  public function tearDown(): void {
    $this->dbh->rollback();
    $this->dbh->close();
  }

  public function testGetSubscriberGrowth() {
    $this->testData->createSubscriptions($this->priceId);
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
    $this->testData->createSubscriberPayments($this->priceId);
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
    $this->testData->createSubscriptions($this->priceId);
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
    $this->testData->createSubscriptions($this->priceId);
    $plans = $this->metricRepository->getPremiumMemberCountByPlan();
    $expectedPlans = [
      [
        'product_name' => 'Test Subscription',
        'product_description' => 'Test subscription for PyAngelo',
        'count' => 1
      ]
    ];
    $this->assertEquals($expectedPlans, $plans);
  }

  public function testGetMemberCountByMonth() {
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

