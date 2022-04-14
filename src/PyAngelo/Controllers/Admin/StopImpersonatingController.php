<?php
namespace PyAngelo\Controllers\Admin;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\PersonRepository;

class StopImpersonatingController extends Controller {
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
    if (!isset($_SESSION['impersonator'])) {
      $this->flash('There is no impersonator!', 'danger');
      $this->response->header('Location: /');
      return $this->response;
    }

    if (! $person = $this->personRepository->getPersonByEmail($_SESSION['impersonator'])) {
      $this->flash('You must be a valid impersonator!', 'danger');
      $this->response->header('Location: /');
      return $this->response;
    }

    $_SESSION['loginEmail'] = $_SESSION['impersonator'];
    unset($_SESSION['impersonator']);

    $this->response->header('Location: /admin');
    return $this->response;
  }
}
