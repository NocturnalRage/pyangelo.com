<?php
namespace PyAngelo\Controllers\Admin;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\PersonRepository;

class ImpersonateUserController extends Controller {
  protected $personRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    PersonRepository $personRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->personRepository = $personRepository;
  }

  public function exec() {
    if (!$this->auth->isAdmin()) {
      $this->flash('You are not authorised!', 'danger');
      $this->response->header('Location: /');
      return $this->response;
    }

    if (! isset($this->request->post['email'])) {
      $this->flash('You must select a person to impersonate!', 'danger');
      $this->response->header('Location: /admin/users');
      return $this->response;
    }

    if (! $person = $this->personRepository->getPersonByEmail($this->request->post['email'])) {
      $this->flash('You must select a valid person to impersonate!', 'danger');
      $this->response->header('Location: /admin/users');
      return $this->response;
    }

    $_SESSION['loginEmail'] = $this->request->post['email'];
    $_SESSION['impersonator'] = $this->auth->person()['email'];

    $this->response->header('Location: /');
    return $this->response;
  }
}
