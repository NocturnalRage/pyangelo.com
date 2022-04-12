<?php
namespace PyAngelo\Controllers\Classes;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\ClassRepository;

class TeacherUpdateController extends Controller {
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
      $this->flash('You must be logged in to update your classes!', 'danger');
      $this->response->header('Location: /login');
      return $this->response;
    }

    if (! $this->auth->crsfTokenIsValid()) {
      $this->flash('Please update your classes from the PyAngelo website!', 'danger');
      $this->response->header('Location: /classes/teacher');
      return $this->response;
    }

    if (! $class = $this->getClassById()) {
      $this->response->header('Location: /page-not-found');
      return $this->response;
    }

    if ($this->auth->personId() != $class['person_id'])
      return $this->redirectToNotOwnerPage();

    if (empty($this->request->post['class_name'])) {
      $_SESSION['errors']['class_name'] = 'You must supply a name for your class.';
    }
    else if (strlen($this->request->post['class_name']) > 100) {
      $_SESSION['errors']['class_name'] = 'The class name must be no more than 100 characters.';
    }

    if (! empty($_SESSION['errors'])) {
      $this->flash('There were some errors. Please fix these below and then submit your changes again.', 'danger');
      $_SESSION['formVars'] = $this->request->post;
      $this->response->header('Location: /classes/teacher/' . $class['class_id'] . '/edit');
      return $this->response;
    }

    $rowsUpdated = $this->classRepository->updateClass(
      $class['class_id'],
      $this->request->post['class_name']
    );

    if ($rowsUpdated != 1) {
      $this->flash("We could not update the class name.", "danger");
    }

    $this->response->header('Location: /classes/teacher/' . $class['class_id']);
    return $this->response;
  }

  private function getClassById() {
    if (! isset($this->request->post['classId'])) {
      return false;
    }

    return $this->classRepository->getClassById(
      $this->request->post['classId']
    );
  }

  private function redirectToNotOwnerPage() {
    $this->flash("You must be the owner of the class to update it.", "danger");
    $this->response->header('Location: /classes/teacher');
    return $this->response;
  }
}
