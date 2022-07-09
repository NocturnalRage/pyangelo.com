<?php
namespace PyAngelo\Controllers\Membership;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use Framework\Billing\StripeWrapper;

class PremiumWelcomeController extends controller {
  protected $stripeWrapper;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    StripeWrapper $stripeWrapper
  ) {
    parent::__construct($request, $response, $auth);
    $this->stripeWrapper = $stripeWrapper;
  }

  public function exec() {
    if (isset($this->request->get['payment_intent'])) {
      $paymentIntent = $this->stripeWrapper->retrievePaymentIntent($this->request->get['payment_intent']);
      if ($paymentIntent->status == 'succeeded')
        return $this->showWelcome();
      else if ($paymentIntent->status == 'processing')
        return $this->showProcessing();
      else if ($paymentIntent->status == 'requires_payment_method')
        return $this->redirectBackToChoosePlan('Payment failed. Please try another payment method.');
      else
        return $this->redirectBackToChoosePlan('Something went wrong. Please try again.');
    }
    return $this->showWelcome();
  }

  private function showWelcome() {
    $this->response->setView('membership/premium-member-welcome.html.php');
    $this->response->setVars(array(
      'pageTitle' => "You've Joined as a PyAngelo Premium Member",
      'metaDescription' => "You have now joined as a PyAngelo premium member and have full access to our website and coding tutorials.",
      'activeLink' => 'Premium Membership',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
    return $this->response;
  }

  private function showProcessing() {
    $this->response->setView('membership/payment-currently-processing.html.php');
    $this->response->setVars(array(
      'pageTitle' => "Your Payment is Currently Processing",
      'metaDescription' => "Your payment is still being processed by the authorities. We will update you when your payment has been received.",
      'activeLink' => 'Premium Membership',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
    return $this->response;
  }

  private function redirectBackToChoosePlan($message) {
    $this->flash($message, "danger");
    $this->response->header("Location: /choose-plan");
    return $this->response;
  }
}
