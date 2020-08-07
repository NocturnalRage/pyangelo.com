<?php
namespace PyAngelo\Controllers\Reference;
use PyAngelo\Controllers\Controller;
use Framework\{Request, Response};
use PyAngelo\Auth\Auth;

class ReferenceController extends Controller {
  public function exec() {
    $this->response->setView('reference/reference.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Reference | PyAngelo',
      'metaDescription' => 'Examples and explanations of the most common functions available in PyAngelo.',
      'activeLink' => 'Reference',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
    return $this->response;
  }
}
