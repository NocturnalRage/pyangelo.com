<?php
namespace PyAngelo\Controllers\Classes;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\ClassRepository;

class TeacherIndexController extends Controller {
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

    $allClasses = $this->classRepository->getTeacherClasses($this->auth->personId());
    $classes = array_filter($allClasses, function($class) {
      return ! $class['archived'];
    });
    $archivedClasses = array_filter($allClasses, function($class) {
      return $class['archived'];
    });
    $this->response->setView('classes/index.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'PyAngelo Classes I Teach',
      'metaDescription' => "View all the classes I teach on PyAngelo.",
      'activeLink' => 'teacher',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'classes' => $classes,
      'archivedClasses' => $archivedClasses
    ));
    $this->addVar('flash');
    return $this->response;
  }

  private function redirectToLoginPage() {
    $this->flash("You must be logged in to view the classes you teach", "danger");
    $this->response->header('Location: /login');
    return $this->response;
  }
}
