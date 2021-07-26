<?php
namespace PyAngelo\Controllers\Profile;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\StripeRepository;
use Framework\Billing\StripeWrapper;

class PaymentMethodUpdateController extends Controller {
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
    $this->response->setView('profile/payment-method-update.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn())
      return $this->notLoggedInMessage();

    if (! $this->auth->crsfTokenIsValid())
      return $this->invalidCrsfTokenMessage();

    if (! $this->auth->hasActiveSubscription())
      return $this->noActiveSubscriptionMessage();

    $person = $this->auth->person();
    $customerId = $person["stripe_customer_id"];
    $customerName = $person["given_name"] . " " . $person["family_name"];
    $subscription = $this->stripeRepository->getCurrentSubscription($person["person_id"]);
    $subscriptionId = $subscription["subscription_id"];

    $setupIntent = $this->stripeWrapper->createSetupIntent(
      $customerId, $subscriptionId
    );

    $clientSecret = $setupIntent->client_secret;

    // Success!!!!
    $this->response->setVars(array(
        'status' => 'success',
        'customerName' => $customerName,
        'clientSecret' => $clientSecret,
        'message' => 'Setup Intent created.'
      ));
    return $this->response;
  }

  private function notLoggedInMessage() {
    $this->response->setVars(array(
      'status' => 'danger',
      'customerName' => 'Error',
      'clientSecret' => 'Error',
      'message' => 'You must be logged in to update your payment details.'
    ));
    return $this->response;
  }

  private function invalidCrsfTokenMessage() {
    $this->response->setVars(array(
      'status' => 'danger',
      'customerName' => 'Error',
      'clientSecret' => 'Error',
      'message' => 'You must update your payment details from the PyAngelo website.'
    ));
    return $this->response;
  }

  private function noActiveSubscriptionMessage() {
    $this->response->setVars(array(
      'status' => 'danger',
      'customerName' => 'Error',
      'clientSecret' => 'Error',
      'message' => 'You must have an active subscription to update your payment details.'
    ));
    return $this->response;
  }
}
