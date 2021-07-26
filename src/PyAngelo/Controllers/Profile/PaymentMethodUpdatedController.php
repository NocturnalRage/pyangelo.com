<?php
namespace PyAngelo\Controllers\Profile;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class PaymentMethodUpdatedController extends Controller {
  public function exec() {
    $this->response->setView('profile/payment-method-updated.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Your Credit Card Details Have Been Updated',
      'metaDescription' => 'Congratulations, your credit card details have been successfully updated.',
      'activeLink' => 'payment-method',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
    return $this->response;
  }
}
