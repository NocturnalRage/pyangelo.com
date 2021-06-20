<?php
namespace PyAngelo\Controllers;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class PrivacyPolicyController extends Controller {
  public function exec() {
    $this->response->setView('privacy-policy.html.php');
    $this->setResponseInfo();
    return $this->response;
  }

  private function setResponseInfo() {
    $this->response->setVars(array(
      'pageTitle' => "Privacy Policy | PyAngelo",
      'metaDescription' => "The PyAngelo Privacy Policy.",
      'activeLink' => 'Privacy',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
  }
}
