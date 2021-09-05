<?php
namespace PyAngelo\Controllers\Classes;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\ClassRepository;

class TeacherRestoreController extends Controller {
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
      $this->flash('You must be logged in to restore a class!', 'danger');
      $this->response->header('Location: /login');
      return $this->response;
    }

    if (! $this->auth->crsfTokenIsValid()) {
      $this->flash('Please restore classes from the PyAngelo website!', 'danger');
      $this->response->header('Location: /classes/teacher');
      return $this->response;
    }

    if (empty($this->request->post['classId'])) {
      $this->flash('You must select a class to restore', 'danger');
      $this->response->header('Location: /classes/teacher');
      return $this->response;
    }

    if (! $class = $this->classRepository->getClassById($this->request->post['classId'])) {
      $this->flash('You must select a valid class to restore!', 'danger');
      $this->response->header('Location: /classes/teacher');
      return $this->response;
    }

    if ($class['person_id'] != $this->auth->personId()) {
      $this->flash('You must be the owner of the class to restore it.', 'danger');
      $this->response->header('Location: /classes/teacher');
      return $this->response;
    }

    $rowsUpdated = $this->classRepository->restoreClass(
      $this->request->post['classId']
    );

    if ($rowsUpdated == 1) {
      $this->flash('Your class has been restored.', 'success');
    }
    else {
      $this->flash('Sorry, we could not restore the class.', 'danger');
    }

    $this->response->header('Location: /classes/teacher');
    return $this->response;
  }
}
