<?php
namespace PyAngelo\Repositories;

interface StripeRepository {
  public function getAllMembershipPlans();

  public function getMembershipPlans($currencyCode);

  public function getPlanById($stripePlanId);

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
    $plan_id,
    $start,
    $status,
    $percentOff
  );

  public function getStripeEvent($eventId);

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
    $planId,
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

  public function cancelSubscriptionAtPeriodEnd($subscriptionId);

  public function resumeSubscription($subscriptionId);

  public function updatePersonLast4($personId, $last4);
}
?>
