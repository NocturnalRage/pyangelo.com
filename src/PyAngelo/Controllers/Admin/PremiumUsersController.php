<?php
namespace PyAngelo\Controllers\Admin;

use Framework\{Request, Response};
use Framework\Contracts\AvatarContract;
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\PersonRepository;

class PremiumUsersController extends Controller {
  protected $personRepository;
  protected $avatar;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    PersonRepository $personRepository,
    AvatarContract $avatar
  ) {
    parent::__construct($request, $response, $auth);
    $this->personRepository = $personRepository;
    $this->avatar = $avatar;
  }

  public function exec() {
    if (!$this->auth->isAdmin()) {
      $this->flash('You are not authorised!', 'danger');
      $this->response->header('Location: /');
      return $this->response;
    }

    $premiumMembers = $this->personRepository->getPremiumMembers();

    $this->response->setView('admin/premium-users.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Premium Users',
      'metaDescription' => "This page shows the current PyAngelo Premium users.",
      'activeLink' => 'premium-users',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'premiumMembers' => $premiumMembers,
      'avatar' => $this->avatar
    ));
    return $this->response;
  }
}
