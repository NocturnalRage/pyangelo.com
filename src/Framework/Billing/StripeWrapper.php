<?php
namespace Framework\Billing;
use Stripe\StripeClient;

class StripeWrapper {

  protected $stripeRepository;

  public function __construct($stripeSecretKey) {
    $this->stripe = new StripeClient($stripeSecretKey);
  }

  public function retrievePrice($stripePriceId) {
    return $this->stripe->prices->retrieve($stripePriceId, []);
  }

  public function createCustomer($email, $name) {
    return $this->stripe->customers->create([
      'email' => $email,
      'name' => $name
    ]);
  }

  public function retrieveCustomer($customerId) {
    return $this->stripe->customers->retrieve($customerId, []);
  }

  public function deleteCustomer($customerId) {
    return $this->stripe->customers->delete($customerId);
  }

  public function createSetupIntent($customerId, $subscriptionId) {
    return $this->stripe->setupIntents->create(
      [
        'customer' => $customerId,
        'metadata' => [ 'subscription_id' => $subscriptionId ]
      ]
    );
  }

  public function retrievePaymentMethod($paymentMethodId) {
    return $this->stripe->paymentMethods->retrieve(
      $paymentMethodId,
      []
    );
  }

  public function createSubscription($customerId, $priceId) {
    return $this->stripe->subscriptions->create([
        'customer' => $customerId,
        'items' => [[
            'price' => $priceId
        ]],
        'payment_behavior' => 'default_incomplete',
        'expand' => ['latest_invoice.payment_intent']
    ]);
  }

  public function retrieveSubscription($subscriptionId) {
    return $this->stripe->subscriptions->retrieve($subscriptionId, []);
  }

  public function updateSubscription($subscriptionId, $changes) {
    return $this->stripe->subscriptions->update(
      $subscriptionId,
      $changes
    );
  }

  public function cancelSubscription($subscriptionId) {
    return $this->stripe->subscriptions->cancel($subscriptionId, []);
  }

  public function retrieveEvent($eventId) {
    return $this->stripe->events->retrieve($eventId, []);
  }

  public function createCharge($amountInCents, $currency, $source, $description) {
    return $this->stripe->charges->create([
      'amount' => $amountInCents,
      'currency' => $currency,
      'source' => $source,
      'description' => $description
    ]);
  }

  public function retrieveCharge($stripeChargeId) {
    return $this->stripe->charges->retrieve($stripeChargeId, []);
  }

  public function retrieveBalanceTransaction($balanceTransactionId) {
    return $this->stripe->balanceTransactions->retrieve($balanceTransactionId, []);
  }

  public function createPaymentIntent($amountInCents, $currency, $paymentMethodTypes) {
    return $this->stripe->paymentIntents->create([
      'amount' => $amountInCents,
      'currency' => $currency,
      'payment_method_types' => $paymentMethodTypes
    ]);
  }

  public function retrievePaymentIntent($paymentIntentId) {
    return $this->stripe->paymentIntents->retrieve($paymentIntentId, []);
  }

  public function extractFeeDetails($balanceTransaction) {
    foreach ($balanceTransaction->fee_details as $feeDetail) {
      if ($feeDetail->type == 'stripe_fee') {
        $fees['stripe'] = $feeDetail->amount;
      }
      else if ($feeDetail->type == 'tax') {
        $fees['tax'] = $feeDetail->amount;
      }
    }
    return $fees;
  }
}
?>
