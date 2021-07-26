<?php
namespace PyAngelo\Controllers\Profile;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class PaymentMethodController extends Controller {
  public function exec() {
    if (! $this->auth->loggedIn()) {
      $this->flash('You must be logged in to update your payment method.', 'danger');
      $this->response->header('Location: /login');
      return $this->response;
    }
    
    $this->response->setView('profile/payment-method.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Update Your Credit Card Details',
      'metaDescription' => 'This page allows you to update the credit card you have stored for your subscriptions.',
      'activeLink' => 'payment-method',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'hasActiveSubscription' => $this->auth->hasActiveSubscription(),
      'person' => $this->auth->person(),
      'stripePublishableKey' => $this->request->env['STRIPE_PUBLISHABLE_KEY']
    ));
    $this->addVar('flash');
    return $this->response;
  }
}
