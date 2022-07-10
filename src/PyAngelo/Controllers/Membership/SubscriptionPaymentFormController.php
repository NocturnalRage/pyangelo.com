<?php
namespace PyAngelo\Controllers\Membership;

use NumberFormatter;
use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use Framework\Billing\StripeWrapper;
use PyAngelo\Repositories\StripeRepository;

class SubscriptionPaymentFormController extends Controller {
  protected $stripeWrapper;
  protected $stripeRepository;
  protected $numberFormatter;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    StripeWrapper $stripeWrapper,
    StripeRepository $stripeRepository,
    NumberFormatter $numberFormatter
  ) {
    parent::__construct($request, $response, $auth);
    $this->stripeWrapper = $stripeWrapper;
    $this->stripeRepository = $stripeRepository;
    $this->numberFormatter = $numberFormatter;
  }

  public function exec() {
    if (! $this->auth->loggedIn())
      return $this->redirectToPremiumMembershipPage();

    $hasActiveSubscription = $this->auth->hasActiveSubscription();
    if ($hasActiveSubscription)
      return $this->redirectToSubscriptionPage();

    if (! isset($this->request->get['priceId']))
      return $this->redirectToPremiumMembershipPageForPrice();

    try {
      $stripePrice = $this->stripeWrapper->retrievePrice($this->request->get['priceId']);
    } catch (\Exception $e) {
      return $this->redirectToPremiumMembershipPageForPrice();
    }
    $pyangeloPrice = $this->stripeRepository->getStripePriceById($stripePrice->id);

    try {
      $customerInfo = $this->getOrCreateCustomer();
      $clientSecret = $this->getOrCreateSubscription($customerInfo, $stripePrice->id);
    } catch (\Exception $e) {
      return $this->redirectToPremiumMembershipPageWithStripeError($e->getMessage());
    }
    

    $this->response->setView('membership/subscription-payment-form.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Subscribe to a Monthly Plan',
      'metaDescription' => 'Enter your payment details to start a monthly subscription plan to the PyAngelo website.',
      'activeLink' => 'Premium Membership',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'stripePublishableKey' => $this->request->env['STRIPE_PUBLISHABLE_KEY'],
      'stripePrice' => $stripePrice,
      'pyangeloPrice' => $pyangeloPrice,
      'numberFormatter' => $this->numberFormatter
    ));
    $this->addVar('flash');
    return $this->response;
  }

  private function redirectToPremiumMembershipPage() {
    $this->flash('You must be logged in to create a subscription!', 'warning');
    $this->response->header('Location: /choose-plan');
    return $this->response;
  }

  private function redirectToPremiumMembershipPageForPrice() {
    $this->flash('You must select a monthly plan!', 'warning');
    $this->response->header('Location: /choose-plan');
    return $this->response;
  }

  private function redirectToSubscriptionPage() {
    $this->flash('You already have full access with your current subscription!', 'warning');
    $this->response->header('Location: /subscription');
    return $this->response;
  }

  private function redirectToPremiumMembershipPageWithStripeError($message) {
    $this->flash($message, 'warning');
    $this->response->header('Location: /choose-plan');
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
