<?php
namespace PyAngelo\Controllers\Registration;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class RegisterThanksController extends Controller {
  public function exec() {
    $this->response->setView('registration/thanks-for-registering.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Thanks for Registering',
      'metaDescription' => "Thanks for signing up to the PyAngelo website. You'll be coding in no time.",
      'activeLink' => 'Home',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
    return $this->response;
  }
}
