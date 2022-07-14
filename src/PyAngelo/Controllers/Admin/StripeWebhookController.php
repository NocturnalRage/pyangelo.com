<?php
namespace PyAngelo\Controllers\Admin;

use NumberFormatter;
use Framework\{Request, Response};
use Framework\Billing\StripeWrapper;
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Email\StripeWebhookEmails;
use PyAngelo\Repositories\StripeRepository;
use PyAngelo\Repositories\PersonRepository;

class StripeWebhookController extends Controller {
  protected $request;
  protected $response;
  protected $stripeWrapper;
  protected $stripeWebhookEmails;
  protected $stripeRepository;
  protected $personRepository;
  protected $stripeWebhookSecret;
  protected $numberFormatter;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    StripeWrapper $stripeWrapper,
    StripeWebhookEmails $stripeWebhookEmails,
    StripeRepository $stripeRepository,
    PersonRepository $personRepository,
    $stripeWebhookSecret,
    NumberFormatter $numberFormatter
  ) {
    parent::__construct($request, $response, $auth);
    $this->stripeWrapper = $stripeWrapper;
    $this->stripeWebhookEmails = $stripeWebhookEmails;
    $this->stripeRepository = $stripeRepository;
    $this->personRepository = $personRepository;
    $this->stripeWebhookSecret = $stripeWebhookSecret;
    $this->numberFormatter = $numberFormatter;
  }

  public function exec() {
    $payload = @file_get_contents('php://input');
    $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $event = null;

    try {
      $event = \Stripe\Webhook::constructEvent(
        $payload, $sigHeader, $this->stripeWebhookSecret
      );
    } catch(\UnexpectedValueException $e) {
      $this->logMessage('Invalid payload sent to Stripe Webhook', 'WARNING');
      $this->sendInvalidPayloadEmail();
      $this->response->header("HTTP/1.1 400 Bad Request");
      return $this->response;
    } catch(\Stripe\Exception\SignatureVerificationException $e) {
      $this->logMessage('Invalid signature sent to Stripe Webhook', 'WARNING');
      $this->sendInvalidSignatureEmail();
      $this->response->header("HTTP/1.1 400 Bad Request");
      return $this->response;
    }

    $eventId = $event->id;
    try { 
      $event = $this->stripeWrapper->retrieveEvent($eventId);
    } catch (\Exception $e) {
      $this->logMessage('Event that does not exist sent to Stripe Webhook', 'WARNING');
      $this->sendNoStripeEventEmail();
      $this->response->header("HTTP/1.1 200 OK");
      return $this->response;
    }

    if ($stripeEvent = $this->stripeRepository->getStripeEventById($eventId)) {
      $this->logMessage('Duplicate event sent to Stripe Webhook', 'WARNING');
      $this->sendStripeEventAlreadyProcessedEmail($eventId);
      $this->response->header("HTTP/1.1 200 OK");
      return $this->response;
    }

    $this->stripeRepository->saveStripeEvent(
      $event->id,
      $event->api_version,
      $event->created,
      $event->type
    );

    if ($event->type == 'invoice.payment_succeeded') {
      $this->handleInvoicePaymentSucceeded($event);
    }
    else if ($event->type == 'invoice.payment_failed') {
      $this->handleInvoicePaymentFailed($event);
    }
    else if ($event->type == 'customer.subscription.updated') {
      $this->handleCustomerSubscriptionUpdated($event);
    }
    else if ($event->type == 'customer.subscription.deleted') {
      $this->handleCustomerSubscriptionDeleted($event);
    }
    else if ($event->type == 'setup_intent.succeeded') {
      $this->handleSetupIntentSucceeded($event);
    }
    else if ($event->type == 'ping') {
      $this->handlePing($event);
    }
    else {
      $this->logMessage('Unhandled Stripe Webhook: ' . $event->type, 'WARNING');
      $this->sendUnhandledWebhookEmail($event->type);
    }

    $this->response->header("HTTP/1.1 200 OK");
    return $this->response;
  }

  public function testWebhook() {
    return 1;
  }

  private function handleInvoicePaymentSucceeded($event) {
    $invoice = $event->data->object;
    $customerId = $invoice->customer;
    $subscriptionId = $invoice->subscription;
    $chargeId = $invoice->charge;
    $currencyCode = $invoice->currency;

    // Get subscription from Stripe
    try {
      $subscription = $this->stripeWrapper->retrieveSubscription($subscriptionId);
    } catch (\Exception $e) {
      $this->logMessage('Subscription that does not exist sent to Stripe Webhook', 'ERROR');
      $this->sendNoStripeSubscriptionEmail($subscriptionId);
      $this->response->header("HTTP/1.1 200 OK");
      return $this->response;
    }

    // Get the person
    $person = $this->stripeRepository->getPersonFromSubscription($subscriptionId);

    // Get charge from Stripe
    try { 
      $charge = $this->stripeWrapper->retrieveCharge($chargeId);
      $balanceTransaction = $this->stripeWrapper->retrieveBalanceTransaction(
        $charge->balance_transaction,
      );
    } catch (\Exception $e) {
      $this->logMessage('Stripe Webhook could not record a charge', 'ERROR');
      $this->sendCouldNotRecordChargeEmail($chargeId);
      $this->response->header("HTTP/1.1 200 OK");
      return $this->response;
    }

    // If this is the first payment
    // update the default payment method on Stripe
    // so future invoices can be automatically paid
    if ($invoice->billing_reason == "subscription_create") {
      $this->subscribeToPremiumNewsletter($person['person_id']);

      $paymentIntentId = $invoice->payment_intent;
      $paymentIntent = $this->stripeWrapper->retrievePaymentIntent(
        $paymentIntentId
      );
      $updatedSubscription = $this->stripeWrapper->updateSubscription(
        $subscription->id,
        ['default_payment_method' => $paymentIntent->payment_method]
      );
    }

    // Update our local database to grant or extend premium membership
    $this->stripeRepository->updatePersonPremiumMemberDetails(
      $person["person_id"],
      $subscription->current_period_end,
      $subscription->customer,
      $charge->payment_method_details->card->last4
    );
    $cancel_at_period_end = 0;
    $this->stripeRepository->updateSubscription(
      $subscription->id,
      $cancel_at_period_end,
      $subscription->current_period_start,
      $subscription->current_period_end,
      $subscription->status
    );
 
    // Record the payment in our local database
    $fees = $this->stripeWrapper->extractFeeDetails($balanceTransaction);
    $this->stripeRepository->insertSubscriptionPayment(
      $invoice->subscription,
      strtoupper($invoice->currency),
      $invoice->amount_due,
      $balanceTransaction->created,
      $fees['stripe'],
      $fees['tax'],
      $balanceTransaction->net,
      $invoice->charge
    );

    // Send payment received email to user
    $currency = $this->stripeRepository->getCurrencyFromCode($currencyCode);
    $amountPaid = $invoice->amount_due / $currency['stripe_divisor'];
    $this->logMessage('Stripe Webhook: Payment recorded for person ' . $person["person_id"], 'INFO');
    $this->sendPaymentReceivedEmail($person, $currency, $amountPaid);
  }

  private function handleInvoicePaymentFailed($event) {
    $invoice = $event->data->object;
    // If there is no next payment the subscription will be cancelled
    // and a customer.subscription.deleted event will be sent and processed
    if (isset($invoice->next_payment_attempt)) {
      $subscriptionId = $invoice->subscription;
      $currencyCode = $invoice->currency;
      try { 
        $subscription = $this->stripeWrapper->retrieveSubscription(
          $subscriptionId
        );
      } catch (\Exception $e) {
        $this->logMessage('Payment failed event sent for unknown subscription to Stripe Webhook', 'ERROR');
        $this->sendNoStripeSubscriptionEmail($subscriptionId);
        $this->response->header("HTTP/1.1 200 OK");
        return $this->response;
      }
      $this->stripeRepository->updateSubscriptionStatus($subscription->id, $subscription->status);
      $person = $this->stripeRepository->getPersonFromSubscription($subscriptionId);
      $currency = $this->stripeRepository->getCurrencyFromCode($currencyCode);
      $amountDue = $invoice->amount_due / $currency['stripe_divisor'];
      $secondsInDay = 60*60*24;
      $retryPaymentDays = round(($invoice->next_payment_attempt - time()) / $secondsInDay);
      $this->sendPaymentFailedEmail(
        $person,
        $currency,
        $amountDue,
        $retryPaymentDays
      );
      $this->logMessage('Stripe Webhook: Payment failed for person ' . $person["person_id"], 'INFO');
    }
  }

  private function handleCustomerSubscriptionUpdated($event) {
    $subscription = $event->data->object;
    $this->stripeRepository->updateSubscriptionStatus($subscription->id, $subscription->status);
    $person = $this->stripeRepository->getPersonFromSubscription($subscription->id);

    $this->logMessage('Stripe Webhook: Subscription ' . $subscription->id . " updated status to " . $subscription->status . " for person " . $person["person_id"], 'INFO');
  }

  private function handleCustomerSubscriptionDeleted($event) {
    $subscription = $event->data->object;
    $this->stripeRepository->updateSubscriptionStatus($subscription->id, $subscription->status);
    $this->stripeRepository->updateCanceledAtIfNull($subscription->id);

    $person = $this->stripeRepository->getPersonFromSubscription($subscription->id);
    $premiumListId = 2;
    $unsubscribedStatus = 2;
    $this->personRepository->updateSubscriber(
      $premiumListId,
      $person['person_id'],
      $unsubscribedStatus
    );
    $this->logMessage('Stripe Webhook: Subscription ' . $subscription->id . " deleted for person " . $person["person_id"], 'INFO');
  }

  private function handleSetupIntentSucceeded($event) {
    $setupIntent = $event->data->object;
    $customerId = $setupIntent->customer;
    $subscriptionId = $setupIntent->metadata->subscription_id;
    if (empty($subscriptionId)) {
      $this->logMessage('Stripe Webhook: SetupIntent Succeeded but without subscription_id attached. No action requried.', 'INFO');
    }
    else {
      $updatedSubscription = $this->stripeWrapper->updateSubscription(
        $subscriptionId,
          ['default_payment_method' => $setupIntent->payment_method]
      );
      $paymentMethod = $this->stripeWrapper->retrievePaymentMethod(
        $setupIntent->payment_method
      );
      $person = $this->stripeRepository->getPersonFromSubscription($subscriptionId);
      $this->stripeRepository->updatePersonLast4($person["person_id"], $paymentMethod->card->last4);
      $this->logMessage('Stripe Webhook: Payment method updated for customer ' . $customerId . " on subscription " . $subscriptionId, 'INFO');
    }
  }

  private function handlePing($event) {
    // Nothing to do, stripe sends to this from time to time.
    $this->sendPingWebhooktEmail();
  }

  private function sendInvalidPayloadEmail() {
    $mailInfo = [
      'emailType' => 'invalidPayload'
    ];
    $this->stripeWebhookEmails->queueEmail($mailInfo);
  }

  private function sendInvalidSignatureEmail() {
    $mailInfo = [
      'emailType' => 'invalidSignature'
    ];
    $this->stripeWebhookEmails->queueEmail($mailInfo);
  }

  private function sendNoStripeEventEmail($eventId) {
    $mailInfo = [
      'emailType' => 'noStripeEvent',
      'stripeEventId' => $eventId
    ];
    $this->stripeWebhookEmails->queueEmail($mailInfo);
  }

  private function sendStripeEventAlreadyProcessedEmail($eventId) {
    $mailInfo = [
      'emailType' => 'stripeEventAlreadyProcessed',
      'stripeEventId' => $eventId
    ];
    $this->stripeWebhookEmails->queueEmail($mailInfo);
  }

  private function sendUnhandledWebhookEmail($eventType) {
    $mailInfo = [
      'emailType' => 'unhandledWebhook',
      'stripeEventType' => $eventType
    ];
    $this->stripeWebhookEmails->queueEmail($mailInfo);
  }

  private function sendNoStripeSubscriptionEmail($subscriptionId) {
    $mailInfo = [
      'emailType' => 'noStripeSubscription',
      'stripeSubscriptionId' => $subscriptionId
    ];
    $this->stripeWebhookEmails->queueEmail($mailInfo);
  }

  private function sendCouldNotRecordChargeEmail($chargeId) {
    $mailInfo = [
      'emailType' => 'couldNotRecordCharge',
      'stripeChargeId' => $chargeId
    ];
    $this->stripeWebhookEmails->queueEmail($mailInfo);
  }

  private function sendPaymentReceivedEmail(
    $person,
    $currency,
    $amountPaid
  ) {
    $amountPaidMoney = $this->numberFormatter->formatCurrency($amountPaid, $currency['currency_code']);
    $mailInfo = [
      'emailType' => 'paymentReceived',
      'toEmail' => $person['email'],
      'givenName' => $person['given_name'],
      'amountPaidMoney' => $amountPaidMoney
    ];
    $this->stripeWebhookEmails->queueEmail($mailInfo);
  }

  private function sendPaymentFailedEmail(
    $person,
    $currency,
    $amountDue,
    $retryPaymentDays
  ) {
    $amountDueMoney = $this->numberFormatter->formatCurrency($amountDue, $currency['currency_code']);
    $mailInfo = [
      'emailType' => 'paymentFailed',
      'toEmail' => $person['email'],
      'givenName' => $person['given_name'],
      'amountDueMoney' => $amountDueMoney,
      'retryPaymentDays' => $retryPaymentDays
    ];
    $this->stripeWebhookEmails->queueEmail($mailInfo);
  }

  private function sendPingWebhookEmail($eventId) {
    $mailInfo = [
      'emailType' => 'pingWebhook',
      'stripeEventId' => $eventId
    ];
    $this->stripeWebhookEmails->queueEmail($mailInfo);
  }

  private function subscribeToPremiumNewsletter($personId) {
    $premiumListId = 2;
    $activeStatus = 1;
    $subscriber = $this->personRepository->getSubscriber($premiumListId, $personId);
    if (!$subscriber) {
      $this->personRepository->insertSubscriber($premiumListId, $personId);
    }
    else {
      $this->personRepository->updateSubscriber(
        $premiumListId,
        $personId,
        $activeStatus
      );
    }
  }
}
