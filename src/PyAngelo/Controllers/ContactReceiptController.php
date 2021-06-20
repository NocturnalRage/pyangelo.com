<?php
namespace PyAngelo\Controllers;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class ContactReceiptController extends Controller{

  public function exec() {
    $this->response->setView('contact-receipt.html.php');
    $this->setResponseInfo();
    return $this->response;
  }

  private function setResponseInfo() {
    $this->response->setVars(array(
      'pageTitle' => "Thanks for contacting us.",
      'metaDescription' => "Thanks for contacting the PyAngelo team. We'll be in touch shortly to answer your inquiry.",
      'activeLink' => 'Home',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
  }
}
