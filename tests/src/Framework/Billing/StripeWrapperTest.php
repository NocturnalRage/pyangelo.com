<?php
namespace Tests\src\Framework\Billing;

use PHPUnit\Framework\TestCase;
use Framework\Billing\StripeWrapper;
use PyAngelo\Auth\Auth;
use Framework\Request;
use Tests\Factory\TestData;

class StripeWrapperTest extends TestCase {

  protected $dbh;
  protected $testData;
  protected $stripeWrapper;
  protected $stripePriceId;
  protected $stripePriceInCents;
   
  public function setUp(): void {
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../../', '.env.test');
    $dotenv->load();
    $this->dbh = new \Mysqli(
      $_ENV['DB_HOST'],
      $_ENV['DB_USERNAME'],
      $_ENV['DB_PASSWORD'],
      $_ENV['DB_DATABASE'] 
    );
    $this->testData = new TestData($this->dbh);
    $this->stripeWrapper = new StripeWrapper(
      $_ENV['STRIPE_SECRET_KEY']
    );
    $this->stripePriceId = 'price_1JEUrpAkvBrl8hmb6AaEIRZN';
    $this->stripePriceInCents = 995;
  }

  public function tearDown(): void {
    $this->dbh->close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->stripeWrapper), 'Framework\Billing\StripeWrapper');
  }

  public function testRetrievePrice() {
    $price = $this->stripeWrapper->retrievePrice($this->stripePriceId);
    $this->assertSame($this->stripePriceId, $price->id);
    $this->assertSame('aud', $price->currency);
    $this->assertSame('month', $price->recurring->interval);
  }

  public function testCreateRetrieveDeleteCustomer() {
    $email = 'joel+stripetest@hotmail.com';
    $name = 'Joel Selwood';
    $customer = $this->stripeWrapper->createCustomer($email, $name);
    $this->assertSame("customer", $customer->object);
    $this->assertSame($email, $customer->email);
    $this->assertSame($name, $customer->name);
    $retrieved = $this->stripeWrapper->retrieveCustomer($customer->id);
    $this->assertSame("customer", $retrieved->object);
    $this->assertSame($retrieved->email, $customer->email);
    $this->assertSame($retrieved->name, $customer->name);
    $customerDel = $this->stripeWrapper->deleteCustomer($customer->id);
    $this->assertSame($customerDel->id, $customer->id);
  }

  public function testCreateSetupIntent() {
    $email = 'joel+stripetest@hotmail.com';
    $name = 'Joel Selwood';
    $customer = $this->stripeWrapper->createCustomer($email, $name);
    // Create the subscription
    $subscription = $this->stripeWrapper->createSubscription(
      $customer->id,
      $this->stripePriceId
    );
    $setupIntent = $this->stripeWrapper->createSetupIntent(
      $customer->id,
      $subscription->id
    );
    $this->assertSame("setup_intent", $setupIntent->object);
    $this->assertSame($setupIntent->customer, $customer->id);
    $this->assertSame($setupIntent->metadata->subscription_id, $subscription->id);
    $customerDel = $this->stripeWrapper->deleteCustomer($customer->id);
  }

  public function testCreateAndRetrieveAndUpdateAndCancelSubscription() {
    // Create the Customer
    $email = 'joel+stripetest@hotmail.com';
    $name = 'Joel Selwood';
    $customer = $this->stripeWrapper->createCustomer($email, $name);
    $this->assertSame($email, $customer->email);
    $this->assertSame($name, $customer->name);

    // Create the subscription
    $subscription = $this->stripeWrapper->createSubscription(
      $customer->id,
      $this->stripePriceId
    );
    $this->assertSame("subscription", $subscription->object);
    $this->assertSame($this->stripePriceId, $subscription->items->data[0]->price->id);
    $this->assertSame($this->stripePriceInCents, $subscription->items->data[0]->price->unit_amount);

    $retrieved = $this->stripeWrapper->retrieveSubscription($subscription->id);
    $this->assertSame("subscription", $retrieved->object);
    $this->assertSame($retrieved->id, $subscription->id);
    $this->assertSame($retrieved->customer, $subscription->customer);

    $orderId = 'fake-order';
    $modified = $this->stripeWrapper->updateSubscription(
      $subscription->id,
      [
        'metadata' => ['order_id' => $orderId],
        'cancel_at_period_end' => true
      ]
    );
    $this->assertSame($modified->id, $subscription->id);
    $this->assertSame($modified->metadata->order_id, $orderId);
    $this->assertSame($modified->cancel_at_period_end, true);

    $modified = $this->stripeWrapper->updateSubscription(
      $subscription->id,
      [
        'cancel_at_period_end' => false
      ]
    );
    $this->assertSame($modified->id, $subscription->id);
    $this->assertSame($modified->cancel_at_period_end, false);

    $this->stripeWrapper->cancelSubscription($subscription->id);
    $canceled = $this->stripeWrapper->retrieveSubscription($subscription->id);
    $this->assertSame('incomplete_expired', $canceled->status);
  }

  public function testRetrieveEvent() {
    $events = $this->stripeWrapper->stripe->events->all(['limit' => 1]);
    $eventId = $events->data[0]->id;
    $event = $this->stripeWrapper->retrieveEvent($eventId);
    $this->assertSame("event", $event->object);
    $this->assertSame($event->id, $eventId);
  }

  public function testCreateAndRetrieveChargeAndBalanceTransaction() {
    $amountInCents = 695;
    $charge = $this->stripeWrapper->createCharge(
      $amountInCents, 'aud', 'tok_visa', 'A test charge'  
    );
    $this->assertSame("charge", $charge->object);
    $this->assertSame($amountInCents, $charge->amount);

    $retrievedCharge = $this->stripeWrapper->retrieveCharge($charge->id);
    $this->assertSame("charge", $retrievedCharge->object);
    $this->assertSame($amountInCents, $retrievedCharge->amount);

    $balanceTransaction = $this->stripeWrapper->retrieveBalanceTransaction(
      $charge->balance_transaction
    );
    $expectedTax = 5;
    $expectedStripe = 49;
    $fees = $this->stripeWrapper->extractFeeDetails($balanceTransaction);
    $this->assertSame($expectedTax, $fees["tax"]);
    $this->assertSame($expectedStripe, $fees["stripe"]);

    $this->assertSame("balance_transaction", $balanceTransaction->object);
    $this->assertSame($amountInCents, $balanceTransaction->amount);
    $this->assertSame($charge->id, $balanceTransaction->source);
  }

  public function testCreateAndRetrievePaymentIntents() {
    $amountInCents = 695;
    $paymentIntent = $this->stripeWrapper->createPaymentIntent(
      $amountInCents, 'aud', ['card']
    );
    $this->assertSame("payment_intent", $paymentIntent->object);
    $this->assertSame($amountInCents, $paymentIntent->amount);
    $retrieved = $this->stripeWrapper->retrievePaymentIntent(
      $paymentIntent->id
    );
    $this->assertSame("payment_intent", $retrieved->object);
    $this->assertSame($amountInCents, $retrieved->amount);
  }

  public function testUpdateEmail() {
    $email = 'joel+stripetest@hotmail.com';
    $updatedEmail = 'joel@geelongfc.com.au';
    $name = 'Joel Selwood';
    $customer = $this->stripeWrapper->createCustomer($email, $name);
    $this->stripeWrapper->updateEmail($customer->id, $updatedEmail);
    $retrieved = $this->stripeWrapper->retrieveCustomer($customer->id);
    $this->assertSame($updatedEmail, $retrieved->email);
  }
}
?>
