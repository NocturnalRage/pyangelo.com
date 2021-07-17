<?php
namespace PyAngelo\Controllers\Admin;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class UsersController extends Controller {
  public function exec() {
    if (!$this->auth->isAdmin()) {
      $this->flash('You are not authorised!', 'danger');
      $this->response->header('Location: /');
      return $this->response;
    }

    $this->response->setView('admin/users.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'User Search',
      'metaDescription' => "Search for a PyAngelo user.",
      'activeLink' => 'users',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
    ));
    $this->addVar('flash');
    return $this->response;
  }
}
