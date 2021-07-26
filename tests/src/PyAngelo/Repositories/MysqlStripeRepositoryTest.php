<?php
namespace Tests\PyAngelo\Repositories;

use PHPUnit\Framework\TestCase;
use PyAngelo\Repositories\MysqlStripeRepository;
use Tests\Factory\TestData;

class MysqlStripeRepositoryTest extends TestCase {
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
    $this->stripeRepository = new MysqlStripeRepository($this->dbh);
    $this->testData = new TestData($this->dbh);
  }

  public function tearDown(): void {
    $this->dbh->close();
  }

  public function testGetCurrencyFromCode() {
    $currencyCode = 'AUD';
    $currencyDescription = 'Australian Dollars';
    $currencySymbol = '$';
    $stripeDivisor = 100;
    $currency = $this->stripeRepository->getCurrencyFromCode($currencyCode);
    $this->assertEquals($currencyCode, $currency['currency_code']);
    $this->assertEquals($currencyDescription, $currency['currency_description']);
    $this->assertEquals($currencySymbol, $currency['currency_symbol']);
    $this->assertEquals($stripeDivisor, $currency['stripe_divisor']);
  }

  public function testGetIncompleteSubscription() {
    $priceId = 'Price1';
    $productId = 'Product1';
    $clientSecret = 'SECRET';
    $productId = 'Product1';
    $personId = 1;
    $email = 'fastfred@hotmail.com';
    $subscriptionId = 'SUB-INCOMPLETE';
    $periodStart = 1482017003;
    $periodEnd = 1484655802;
    $periodStartUpdated = 1482017003;
    $periodEndUpdated = 1487334202;
    $stripeCustomerId = 'CUS-1';
    $start_date = 1482017003;
    $status = 'incomplete';
    $currencyCode = 'USD';
    $totalAmount = 1000;
    $paidAt = 1482017003;
    $stripeFee = 24;
    $taxFee = 2;
    $net = 674;

    $this->testData->deleteAllSubscriptions();
    $this->testData->createPerson($personId, $email);
    $this->testData->createPrice($priceId, $productId);
    $rowsInserted = $this->stripeRepository->insertSubscription(
      $subscriptionId,
      $personId,
      $periodStart,
      $periodEnd,
      $stripeCustomerId,
      $priceId,
      $clientSecret,
      $start_date,
      $status,
      0
    );
    $this->assertEquals(1, $rowsInserted);

    $subscription = $this->stripeRepository->getIncompleteSubscription(
      $personId,
      $priceId
    );
    $this->assertEquals($status, $subscription['status']);
    $this->assertEquals($subscriptionId, $subscription['subscription_id']);
    $this->assertEquals($clientSecret, $subscription['stripe_client_secret']);
  }

  public function testInsertAndUpdateSubscription() {
    $priceId = 'Price1';
    $productId = 'Product1';
    $clientSecret = 'SECRET';
    $personId = 1;
    $email = 'fastfred@hotmail.com';
    $subscriptionId = 'SUB-1';
    $periodStart = 1482017003;
    $periodEnd = 1484655802;
    $periodStartUpdated = 1482017003;
    $periodEndUpdated = 1487334202;
    $stripeCustomerId = 'CUS-1';
    $start_date = 1482017003;
    $status = 'active';
    $currencyCode = 'USD';
    $totalAmount = 1000;
    $paidAt = 1482017003;
    $stripeFee = 24;
    $taxFee = 2;
    $net = 674;
    $chargeId = 'CH_00000000';
    $refundId = 'RE_00000000';

    $this->testData->deleteAllSubscriptions();
    $this->testData->createPerson($personId, $email);
    $this->testData->createPrice($priceId, $productId);
    $rowsInserted = $this->stripeRepository->insertSubscription(
      $subscriptionId,
      $personId,
      $periodStart,
      $periodEnd,
      $stripeCustomerId,
      $priceId,
      $clientSecret,
      $start_date,
      $status,
      0
    );
    $this->assertEquals(1, $rowsInserted);
    $rowsUpdated = $this->stripeRepository->updateSubscription(
      $subscriptionId,
      $periodStartUpdated,
      $periodEndUpdated,
      $status
    );
    $this->assertEquals(1, $rowsUpdated);
    $person = $this->stripeRepository->getPersonFromSubscription(
      $subscriptionId
    );
    $this->assertEquals($personId, $person['person_id']);

    $rowsInserted = $this->stripeRepository->insertSubscriptionPayment(
      $subscriptionId,
      $currencyCode,
      $totalAmount,
      $paidAt,
      $stripeFee,
      $taxFee,
      $net,
      $chargeId
    );
    $this->assertEquals(1, $rowsInserted);

    $rowsInserted = $this->stripeRepository->insertSubscriptionRefund(
      $subscriptionId,
      $currencyCode,
      $totalAmount * -1,
      $paidAt,
      $stripeFee *-1,
      $taxFee * -1,
      $net * -1,
      $refundId,
      $chargeId
    );
    $this->assertEquals(1, $rowsInserted);

    $payment = $this->stripeRepository->getSubscriptionPayment($chargeId);
    $this->assertEquals($totalAmount, $payment['total_amount_in_cents']);
    $this->assertEquals($stripeFee, $payment['stripe_fee_aud_in_cents']);
    $this->assertEquals($taxFee, $payment['tax_fee_aud_in_cents']);

    $refunds = $this->stripeRepository->getRefunds($chargeId);
    $this->assertEquals($refunds['refund_amount'], $totalAmount * -1);

    $rowsUpdated = $this->stripeRepository->updateRefundStatusForPayment(
      $chargeId,
      'FULL'
    );
    $this->assertEquals(1, $rowsUpdated);

    $rowsUpdated = $this->stripeRepository->updateSubscriptionStatus(
      $subscriptionId,
      'past_due'
    );
    $this->assertEquals(1, $rowsUpdated);
    $subscription = $this->stripeRepository->getCurrentSubscription($personId);
    $expectedSubscription = [
      'subscription_id' => $subscriptionId,
      'start_date' => '2016-12-18 10:23:23',
      'current_period_start' => '2016-12-18 10:23:23',
      'current_period_end' => '2017-02-17 23:23:22',
      'stripe_customer_id' => 'CUS-1',
      'stripe_price_id' => 'Price1',
      'status' => 'past_due',
      'percent_off' => 0,
      'cancel_at_period_end' => 0,
      'product_name' => 'Test Subscription',
      'product_description' => 'Test subscription for PyAngelo',
      'currency_code' => 'USD',
      'price_in_cents' => 695,
      'billing_period' => 'month',
      'currency_description' => 'US Dollars',
      'currency_symbol' => '$',
      'stripe_divisor' => 100,
    ];
    $this->assertEquals($expectedSubscription, $subscription);

    $rowsUpdated = $this->stripeRepository->updateCanceledAtIfNull(
      $subscriptionId
    );
    $this->assertEquals(1, $rowsUpdated);
    $rowsUpdated = $this->stripeRepository->updateCanceledAtIfNull(
      $subscriptionId
    );
    $this->assertEquals(0, $rowsUpdated);

    $rowsUpdated = $this->stripeRepository->updateSubscriptionStatus(
      $subscriptionId, 'canceled'
    );
    $this->assertEquals(1, $rowsUpdated);
    $pastSubscriptions = $this->stripeRepository->getPastSubscriptions($personId);
    $this->assertEquals('canceled', $pastSubscriptions[0]['status']);
    $this->assertEquals('SUB-1', $pastSubscriptions[0]['subscription_id']);
  }

  public function testUpdateStripeCustomerId() {
    $personId = 100;
    $email = 'fastfred@hotmail.com';
    $this->testData->createPerson($personId, $email);
    $rowsUpdated = $this->stripeRepository->updateStripeCustomerId(
      $personId,
      'CUS_00000000'
    );
    $this->assertEquals(1, $rowsUpdated);

  }

  public function testUpdatePersonPremiumMemberDetails() {
    $personId = 100;
    $newLast4 = '1881';
    $email = 'fastfred@hotmail.com';
    $this->testData->createPerson($personId, $email);
    $rowsUpdated = $this->stripeRepository->updatePersonPremiumMemberDetails(
      $personId,
      1484655802,
      'CUS_00000000',
      '4242'
    );
    $this->assertEquals(1, $rowsUpdated);

    $this->stripeRepository->updatePersonLast4($personId, $newLast4);
    $this->assertEquals(1, $rowsUpdated);
  }

  public function testGetandSaveStripeEvents() {
    $this->testData->deleteAllStripeEvents();
    $eventId = 'EVT_00000000';
    $apiVersion = '2016-10-07';
    $createdAt = 1482017003;
    $createdAtDate = '2016-12-18 10:23:23';
    $eventType = 'invoice.payment_succeeded';
    $rowsInserted = $this->stripeRepository->saveStripeEvent(
      $eventId,
      $apiVersion,
      $createdAt,
      $eventType
    );
    $this->assertEquals(1, $rowsInserted);
    $stripeEvent = $this->stripeRepository->getStripeEventById($eventId);
    $this->assertEquals($eventId, $stripeEvent['event_id']);
    $this->assertEquals($apiVersion, $stripeEvent['api_version']);
    $this->assertEquals($createdAtDate, $stripeEvent['created_at']);
    $this->assertEquals($eventType, $stripeEvent['event_type']);
  }
}
