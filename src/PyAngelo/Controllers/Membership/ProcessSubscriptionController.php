<?php
namespace PyAngelo\Controllers\Membership;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\StripeRepository;
use Framework\Billing\StripeWrapper;

class ProcessSubscriptionController extends Controller {
  protected $stripeWrapper;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    StripeWrapper $stripeWrapper,
    StripeRepository $stripeRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->stripeWrapper = $stripeWrapper;
    $this->stripeRepository = $stripeRepository;
  }

  public function exec() {
    $this->response->setView('membership/create-subscription.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn())
      return $this->invalidRequest(
        'login-error',
        'You must be logged in to start a subscription'
      );

    if ($this->auth->hasActiveSubscription())
      return $this->invalidRequest(
        'active-subscription',
        'You have an active subscription'
      );

    if (! $this->auth->crsfTokenIsValid())
      return $this->invalidRequest(
        'crsf-error',
        'Please sign up from the PyAngelo website'
      );

    if (! isset($this->request->post['priceId']))
      return $this->invalidRequest(
        'post-error',
        'Price not provided'
      );
    $priceId = $this->request->post['priceId'];

    try {
      $stripePrice = $this->stripeWrapper->retrievePrice($priceId);
    } catch (\Exception $e) {
      return $this->invalidRequest(
        'stripe-price-error',
        'Price not recognised by Stripe'
      );
    }
    $pyangeloPrice = $this->stripeRepository->getStripePriceById($stripePrice->id);
    if (is_null($pyangeloPrice))
      return $this->invalidRequest(
        'pyangelo-error',
        'Price not recognised by PyAngelo'
      );

    try {
      $customerInfo = $this->getOrCreateCustomer();
      $subscriptionInfo = $this->getOrCreateSubscription($customerInfo, $priceId);
    } catch (\Exception $e) {
      return $this->invalidRequest(
        'stripe-error',
        $e->getMessage()
      );
    }

    // Success!!!!
    $this->response->setVars(array(
        'status' => 'success',
        'customerId' => $customerInfo['customerId'],
        'customerName' => $customerInfo['customerName'],
        'subscriptionId' => $subscriptionInfo['subscriptionId'],
        'priceId' => $priceId,
        'clientSecret' => $subscriptionInfo['clientSecret'],
        'message' => 'Subscription created'
      ));
    return $this->response;
  }

  private function invalidRequest($status, $message) {
    $this->response->setVars(array(
      'status' => $status,
      'customerId' => 'Error',
      'customerName' => 'Error',
      'subscriptionId' => 'Error',
      'priceId' => 'Error',
      'clientSecret' => 'Error',
      'message' => $message
    ));
    return $this->response;
  }

  private function getOrCreateCustomer() {
    $person = $this->auth->person();
    $customerName = $person["given_name"] . " " . $person["family_name"];
    if ($this->auth->stripeCustomerId()) {
      $customerId = $this->auth->stripeCustomerId();
    }
    else {
      $customer = $this->stripeWrapper->createCustomer($person["email"], $customerName);
      $customerId = $customer->id;
      $this->stripeRepository->updateStripeCustomerId(
        $person["person_id"],
        $customerId
      );
    }
    return [
      "personId" => $person["person_id"],
      "customerId" => $customerId,
      "customerName" => $customerName
    ];
  }

  private function getOrCreateSubscription($customerInfo, $priceId) {
    if ($subscription = $this->stripeRepository->getIncompleteSubscription($customerInfo["personId"], $priceId)) {
      $clientSecret = $subscription["stripe_client_secret"];
      $subscriptionId = $subscription["subscription_id"];
    }
    else {
      $subscription = $this->stripeWrapper->createSubscription(
        $customerInfo["customerId"],
        $priceId
      );
      $clientSecret = $subscription->latest_invoice->payment_intent->client_secret;
      $subscriptionId = $subscription->id;
      $rowsInserted = $this->stripeRepository->insertSubscription(
        $subscription->id,
        $customerInfo["personId"],
        $subscription->current_period_start,
        $subscription->current_period_end,
        $subscription->customer,
        $priceId,
        $clientSecret,
        $subscription->start_date,
        $subscription->status,
        0
      );
    }
    return [
      "clientSecret" => $clientSecret,
      "subscriptionId" => $subscriptionId
    ];
  }
}
