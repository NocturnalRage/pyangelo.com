<?php
namespace PyAngelo\Repositories;

interface StripeRepository {
  public function updateStripeCustomerId($personId, $stripeCustomerId);

  public function getMembershipPrices($currencyCode);

  public function updatePersonPremiumMemberDetails(
    $personId,
    $endDate,
    $stripeCustomerId,
    $last4
  );

  public function insertSubscription(
    $subscription_id,
    $person_id,
    $current_period_start,
    $current_period_end,
    $customer_id,
    $price_id,
    $stripe_client_secret,
    $start,
    $status,
    $percentOff
  );

  public function getStripeEventById($eventId);

  public function saveStripeEvent(
    $eventId,
    $apiVersion,
    $createdAt,
    $eventType
  );

  public function getCurrencyFromCode($currencyCode);

  public function getPersonFromSubscription($subscriptionId);

  public function updateSubscription(
    $subscriptionId,
    $periodStart,
    $periodEnd,
    $status
  );

  public function insertSubscriptionPayment(
    $subscriptionId,
    $currencyCode,
    $totalAmount,
    $paidAt,
    $stripeFee,
    $taxFee,
    $net,
    $chargeId
  );

  public function insertSubscriptionRefund(
    $subscriptionId,
    $currencyCode,
    $totalAmount,
    $paidAt,
    $stripeFee,
    $taxFee,
    $net,
    $refundId,
    $chargeId
  );

  public function updateRefundStatusForPayment($chargeId, $refundStatus);

  public function getSubscriptionPayment($chargeId);

  public function getRefunds($chargeId);

  public function updateSubscriptionStatus($subscriptionId, $status);

  public function updateCanceledAtIfNull($subscriptionId);

  public function getCurrentSubscription($personId);

  public function getPastSubscriptions($personId);

  public function getIncompleteSubscription($personId, $stripePriceId);

  public function updatePersonLast4($personId, $last4);

  public function getStripePriceById($priceId);
}
?>
