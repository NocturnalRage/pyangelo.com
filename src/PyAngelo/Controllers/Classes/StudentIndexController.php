<?php
namespace PyAngelo\Controllers\Classes;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\ClassRepository;

class StudentIndexController extends Controller {
  protected $classRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    classRepository $classRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->classRepository = $classRepository;
  }

  public function exec() {
    if (! $this->auth->loggedIn())
      return $this->redirectToLoginPage();

    $classes = $this->classRepository->getStudentClasses($this->auth->personId());
    $this->response->setView('classes/student-index.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'My Classes',
      'metaDescription' => "PyAngelo classes I am enrolled in as a student.",
      'activeLink' => 'student',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'classes' => $classes
    ));
    $this->addVar('flash');
    return $this->response;
  }

  private function redirectToLoginPage() {
    $this->flash("You must be logged in to view your classes.", "danger");
    $this->response->header('Location: /login');
    return $this->response;
  }
}
