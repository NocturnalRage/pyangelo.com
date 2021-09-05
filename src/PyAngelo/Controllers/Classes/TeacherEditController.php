<?php
namespace PyAngelo\Controllers\Classes;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\ClassRepository;

class TeacherEditController extends Controller {
  protected $blogRepository;

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
    if (!$this->auth->loggedIn())
      return $this->redirectToLoginPage();

    if (!isset($this->request->get['classId']))
      return $this->redirectToPageNotFound();

    if (!($class = $this->classRepository->getClassById($this->request->get['classId'])))
      return $this->redirectToPageNotFound();

    if ($this->auth->personId() != $class['person_id'])
      return $this->redirectToNotOwnerPage();

    $formVars = $this->request->session['formVars'] ?? $class;
    unset($this->request->session['formVars']);

    $this->response->setView('classes/edit.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Edit ' . $class['class_name'] . ' Class',
      'metaDescription' => "Edit this class.",
      'activeLink' => 'teacher',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'class' => $class,
      'formVars' => $formVars,
      'submitButtonText' => 'Update'
    ));
    $this->addVar('errors');
    $this->addVar('flash');
    return $this->response;
  }

  private function redirectToLoginPage() {
    $this->flash('You must be logged in to edit a class!', 'danger');
    $this->response->header('Location: /login');
    return $this->response;
  }

  private function redirectToPageNotFound() {
    $this->response->header('Location: /page-not-found');
    return $this->response;
  }

  private function redirectToNotOwnerPage() {
    $this->flash("You must be the owner of the class to view it.", "danger");
    $this->response->header('Location: /classes/teacher');
    return $this->response;
  }
}
