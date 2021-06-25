<?php
namespace PyAngelo\Controllers\Reference;
use PyAngelo\Controllers\Controller;
use Framework\{Request, Response};
use PyAngelo\Auth\Auth;

class AssetLibraryController extends Controller {
  public function exec() {
    $this->response->setView('reference/asset-library.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Asset Library | PyAngelo',
      'metaDescription' => 'PyAngelo has a asset library of sample images, music, and sounds that can be used without needing to upload your own.',
      'activeLink' => 'Asset Library',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
    return $this->response;
  }
}
