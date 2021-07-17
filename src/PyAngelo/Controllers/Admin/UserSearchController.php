<?php
namespace PyAngelo\Controllers\Admin;

use Framework\{Request, Response};
use Framework\Contracts\AvatarContract;
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\PersonRepository;

class UserSearchController extends Controller {
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

    if (isset($this->request->get['search'])) {
      $people = $this->personRepository->searchByNameAndEmail(
        $this->request->get['search']
      );
    }
    else {
      $people = [];
    }

    $this->response->setView('admin/user-search.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'User Search Results',
      'metaDescription' => "This page shows the users based on the search criteria.",
      'activeLink' => 'users',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'people' => $people,
      'avatar' => $this->avatar
    ));
    return $this->response;
  }
}
