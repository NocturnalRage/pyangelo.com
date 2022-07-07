<?php
namespace PyAngelo\Controllers\Classes;

use Carbon\Carbon;
use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\ClassRepository;

class TeacherShowController extends Controller {
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
      return $this->redirectToLoginPage();
    }

    if (!isset($this->request->get['classId']))
      return $this->redirectToPageNotFound();

    if (! $class = $this->classRepository->getClassById($this->request->get['classId']))
      return $this->redirectToPageNotFound();

    if ($this->auth->personId() != $class['person_id'])
      return $this->redirectToNotOwnerPage();

    $this->response->setView('classes/show.html.php');
    $this->response->setVars(array(
      'pageTitle' => $class['class_name'],
      'metaDescription' => $class['class_name'],
      'activeLink' => 'teacher',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'class' => $class,
      'students' => $this->classRepository->getClassStudents($this->request->get['classId'])
    ));
    $this->addVar('flash');
    return $this->response;
  }

  private function redirectToLoginPage() {
    $this->flash("You must be logged in to view a class", "danger");
    $_SESSION['redirect'] = $this->request->server['REQUEST_URI'];
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
