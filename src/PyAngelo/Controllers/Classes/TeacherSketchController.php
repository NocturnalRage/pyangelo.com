<?php
namespace PyAngelo\Controllers\Classes;

use Carbon\Carbon;
use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\ClassRepository;

class TeacherSketchController extends Controller {
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

    if (!isset($this->request->get['personId']))
      return $this->redirectToPageNotFound();

    if (! $class = $this->classRepository->getClassById($this->request->get['classId']))
      return $this->redirectToPageNotFound();

    if ($this->auth->personId() != $class['person_id'])
      return $this->redirectToNotOwnerPage();

    if (!$student = $this->classRepository->getStudentFromClass($this->request->get['classId'], $this->request->get['personId']))
      return $this->redirectToStudentNotInClass();

    $sketches = $this->classRepository->getStudentSketches($this->request->get['classId'], $this->request->get['personId']);

    $this->response->setView('classes/student-sketches.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Sketches - ' . $student['given_name'] . ' ' . $student['family_name'],
      'metaDescription' => 'The sketches of ' . $student['given_name'] . ' ' . $student['family_name'],
      'activeLink' => 'teacher',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'class' => $class,
      'student' => $student,
      'sketches' => $sketches
    ));
    $this->addVar('flash');
    return $this->response;
  }

  private function redirectToLoginPage() {
    $this->flash("You must be logged in to view a student's sketches", "danger");
    $this->request->session['redirect'] = $this->request->server['REQUEST_URI'];
    $this->response->header('Location: /login');
    return $this->response;
  }

  private function redirectToPageNotFound() {
    $this->response->header('Location: /page-not-found');
    return $this->response;
  }

  private function redirectToNotOwnerPage() {
    $this->flash("You must be the owner of a class to view the student's sketches.", "danger");
    $this->response->header('Location: /classes/teacher');
    return $this->response;
  }

  private function redirectToStudentNotInClass() {
    $this->flash("The student is not in your class.", "danger");
    $this->response->header('Location: /classes/teacher/' . $this->request->get['classId']);
    return $this->response;
  }
}
