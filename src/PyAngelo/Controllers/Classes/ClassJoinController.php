<?php
namespace PyAngelo\Controllers\Classes;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\ClassRepository;

class ClassJoinController extends Controller {
  protected $classRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    ClassRepository $classRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->classRepository = $classRepository;
  }

  public function exec() {
    if (! $this->auth->loggedIn()) {
      $this->flash('You must be logged in to join a class.', 'danger');
      $this->response->header('Location: /login');
      return $this->response;
    }

    if (empty($this->request->get['joinCode'])) {
      $this->flash("You need a class code to be able to join it.", "danger");
      $this->response->header('Location: /');
      return $this->response;
    }

    if (! $class = $this->classRepository->getClassByCode($this->request->get['joinCode'])) {
      $this->flash("There is no such class to join.", "danger");
      $this->response->header('Location: /');
      return $this->response;
    }

    $rowsInserted = $this->classRepository->joinClass(
      $class['class_id'],
      $this->auth->personId()
    );

    if ($rowsInserted != 1) {
      $this->flash("We could not enrol you in the class.", "danger");
      $this->response->header('Location: /');
      return $this->response;
    }

    $this->response->header('Location: /classes/student');
    return $this->response;
  }
}
