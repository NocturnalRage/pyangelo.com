<?php
namespace PyAngelo\Repositories;

class MysqlStripeRepository implements StripeRepository {
  protected $dbh;

  public function __construct(\Mysqli $dbh) {
    $this->dbh = $dbh;
  }
  public function getAllMembershipPlans() {
    $sql = "SELECT *
            FROM   membership_plan
            ORDER BY currency_code, billing_period_in_months";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getMembershipPlans($currencyCode) {
    $sql = "SELECT *
            FROM   membership_plan
            WHERE  currency_code = ?
            AND    active = TRUE
            ORDER BY billing_period_in_months";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $currencyCode);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getPlanById($stripePlanId) {
    $sql = "SELECT *
	          FROM   membership_plan
            WHERE  stripe_plan_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $stripePlanId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function updatePersonPremiumMemberDetails(
    $personId,
    $endDate,
    $stripeCustomerId,
    $last4
  ) {
    $sql = "UPDATE person
            SET    premium_end_date = date_add(from_unixtime(?), INTERVAL 2 HOUR),
                   stripe_customer_id = ?,
                   last4 = ?,
                   updated_at = now()
            WHERE  person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('issi', $endDate, $stripeCustomerId, $last4, $personId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

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
  ) {
    $sql = "INSERT INTO stripe_subscription(
              subscription_id,
              person_id,
              cancel_at_period_end,
              canceled_at,
              current_period_start,
              current_period_end,
              stripe_customer_id,
              stripe_plan_id,
              start,
              status,
              percent_off,
              created_at,
              updated_at
            )
            VALUES (?, ?, 0, NULL, from_unixtime(?), from_unixtime(?), ?, ?, from_unixtime(?), ?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'siiissisi',
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
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function getStripeEvent($eventId) {
    $sql = "SELECT *
	        FROM   stripe_event
            WHERE  event_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $eventId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function saveStripeEvent(
    $eventId,
    $apiVersion,
    $createdAt,
    $eventType
  ) {
    $sql = "INSERT INTO stripe_event(
              event_id,
              api_version,
              created_at,
              event_type
            )
            VALUES (?, ?, from_unixtime(?), ?)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'ssis',
      $eventId,
      $apiVersion,
      $createdAt,
      $eventType
    );
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function getCurrencyFromCode($currencyCode) {
    $sql = "SELECT *
	        FROM   currency
            WHERE  currency_code = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $currencyCode);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getPersonFromSubscription($subscriptionId) {
    $sql = "SELECT *
	          FROM   person
            WHERE  person_id = (
              SELECT person_id
              FROM   stripe_subscription
              WHERE  subscription_id = ?
            )";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $subscriptionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function updateSubscription(
    $subscriptionId,
    $periodStart,
    $periodEnd,
    $planId,
    $status
  ) {
    $sql = "UPDATE stripe_subscription
            SET    current_period_start = from_unixtime(?),
                   current_period_end = from_unixtime(?),
                   stripe_plan_id = ?,
                   status = ?,
                   updated_at = now()
            WHERE  subscription_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'iisss',
      $periodStart,
      $periodEnd,
      $planId,
      $status,
      $subscriptionId
    );
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function insertSubscriptionPayment(
    $subscriptionId,
    $currencyCode,
    $totalAmount,
    $paidAt,
    $stripeFee,
    $taxFee,
    $net,
    $chargeId
  ) {
    $sql = "INSERT INTO stripe_subscription_payment(
              subscription_id,
              payment_type_id,
              currency_code,
              total_amount_in_cents,
              paid_at,
              stripe_fee_aud_in_cents,
              tax_fee_aud_in_cents,
              net_aud_in_cents,
              charge_id
            )
            VALUES (?, 1, ?, ?, from_unixtime(?), ?, ?, ?, ?)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'ssiiiiis',
      $subscriptionId,
      $currencyCode,
      $totalAmount,
      $paidAt,
      $stripeFee,
      $taxFee,
      $net,
      $chargeId
    );
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

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
  ) {
    $sql = "INSERT INTO stripe_subscription_payment(
              subscription_id,
              payment_type_id,
              currency_code,
              total_amount_in_cents,
              paid_at,
              stripe_fee_aud_in_cents,
              tax_fee_aud_in_cents,
              net_aud_in_cents,
              charge_id,
              original_charge_id
            )
            VALUES (?, 2, ?, ?, from_unixtime(?), ?, ?, ?, ?, ?)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'ssiiiiiss',
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
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function updateRefundStatusForPayment($chargeId, $refundStatus) {
    $sql = "UPDATE stripe_subscription_payment
            SET    refund_status = ?
            WHERE  charge_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ss', $refundStatus, $chargeId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function getSubscriptionPayment($chargeId) {
    $sql = "SELECT *
	          FROM   stripe_subscription_payment
            WHERE  charge_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $chargeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getRefunds($chargeId) {
    $sql = "SELECT sum(total_amount_in_cents) refund_amount
	          FROM   stripe_subscription_payment
            WHERE  original_charge_id = ?
            AND    payment_type_id = 2";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $chargeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function updateSubscriptionStatus($subscriptionId, $status) {
    $sql = "UPDATE stripe_subscription
            SET    status = ?
            WHERE  subscription_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ss', $status, $subscriptionId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function updateCanceledAtIfNull($subscriptionId) {
    $sql = "UPDATE stripe_subscription
            SET    canceled_at = now(),
                   updated_at = now()
            WHERE  subscription_id = ?
            AND    canceled_at IS NULL";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $subscriptionId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function getCurrentSubscription($personId) {
    $sql = "SELECT ss.subscription_id,
                   ss.start,
                   ss.current_period_start,
                   ss.current_period_end,
                   ss.stripe_customer_id,
                   ss.stripe_plan_id,
                   ss.status,
                   ss.percent_off,
                   ss.cancel_at_period_end,
                   mp.display_plan_name,
                   mp.currency_code,
                   mp.price_in_cents,
                   mp.billing_period_in_months,
                   c.currency_description,
                   c.currency_symbol,
                   c.stripe_divisor
            FROM   stripe_subscription ss
            JOIN   membership_plan mp on mp.stripe_plan_id = ss.stripe_plan_id
            JOIN   currency c on mp.currency_code = c.currency_code
            WHERE  ss.person_id = ?
            AND    ss.status in ('active', 'past_due')";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function cancelSubscriptionAtPeriodEnd($subscriptionId) {
    $sql = "UPDATE stripe_subscription
            SET    canceled_at = now(),
                   cancel_at_period_end = 1,
                   updated_at = now()
            WHERE  subscription_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $subscriptionId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function resumeSubscription($subscriptionId) {
    $sql = "UPDATE stripe_subscription
            SET    canceled_at = NULL,
                   cancel_at_period_end = 0,
                   updated_at = now()
            WHERE  subscription_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $subscriptionId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function updatePersonLast4($personId, $last4) {
    $sql = "UPDATE person
            SET    last4 = ?,
                   updated_at = now()
            WHERE  person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('si', $last4, $personId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }
}
?>
