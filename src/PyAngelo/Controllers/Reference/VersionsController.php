<?php
namespace PyAngelo\Controllers\Reference;
use PyAngelo\Controllers\Controller;
use Framework\{Request, Response};
use PyAngelo\Auth\Auth;

class VersionsController extends Controller {
  public function exec() {
    $this->response->setView('reference/versions.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Versions | PyAngelo',
      'metaDescription' => 'A history of the changes made to the PyAngelo website over time.',
      'activeLink' => 'Reference',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
    return $this->response;
  }
}
