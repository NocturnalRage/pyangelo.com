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
      return $this->notLoggedInMessage();

    if (! $this->auth->crsfTokenIsValid())
      return $this->invalidCrsfTokenMessage();

    if (! isset($this->request->post['priceId']))
      return $this->invalidPriceIdMessage();

    $priceId = $this->request->post['priceId'];
    // TODO: Check price exists in PyAngelo database

    if ($this->auth->hasActiveSubscription())
      return $this->activeSubscriptionMessage();

    $customerInfo = $this->getOrCreateCustomer();
    $clientSecret = $this->getOrCreateSubscription($customerInfo, $priceId);

    // Success!!!!
    $this->response->setVars(array(
        'status' => 'success',
        'customerId' => $customerInfo['customerId'],
        'customerName' => $customerInfo['customerName'],
        'clientSecret' => $clientSecret,
        'message' => 'Subscription created.'
      ));
    return $this->response;

  }

  private function invalidCrsfTokenMessage() {
    $this->response->setVars(array(
      'status' => 'danger',
      'customerId' => 'Error',
      'customerName' => 'Error',
      'clientSecret' => 'Error',
      'message' => 'You must become a premium member from the PyAngelo website.'
    ));
    return $this->response;
  }

  private function notLoggedInMessage() {
    $this->response->setVars(array(
      'status' => 'danger',
      'customerId' => 'Error',
      'customerName' => 'Error',
      'clientSecret' => 'Error',
      'message' => 'You must be logged in to become a premium member.'
    ));
    return $this->response;
  }

  private function activeSubscriptionMessage() {
    $this->response->setVars(array(
      'status' => 'danger',
      'customerId' => 'Error',
      'customerName' => 'Error',
      'clientSecret' => 'Error',
      'message' => 'You still have an active subscription.'
    ));
    return $this->response;
  }

  private function invalidPriceIdMessage() {
    $this->response->setVars(array(
      'status' => 'danger',
      'customerId' => 'Error',
      'customerName' => 'Error',
      'clientSecret' => 'Error',
      'message' => 'You must select a price for the subscription.'
    ));
    return $this->response;
  }

  private function getOrCreateCustomer() {
    // Get Customer Details or Create Customer
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
    }
    else {
      $subscription = $this->stripeWrapper->createSubscription(
        $customerInfo["customerId"],
        $priceId
      );
      $clientSecret = $subscription->latest_invoice->payment_intent->client_secret;
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
    return $clientSecret;
  }
}
