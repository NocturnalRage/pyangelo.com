<?php
namespace PyAngelo\Controllers\Membership;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class PremiumWelcomeController extends controller {
  public function exec() {
    $this->response->setView('membership/premium-member-welcome.html.php');
    $this->response->setVars(array(
      'pageTitle' => "You've Joined as a PyAngelo Premium Member",
      'metaDescription' => "You have now joined as a PyAngelo premium member and have full access to our website and coding tutorials.",
      'activeLink' => 'Premium Membership',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
    return $this->response;
  }
}
