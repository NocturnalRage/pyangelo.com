<?php
namespace PyAngelo\Controllers\Classes;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\ClassRepository;

class TeacherNewController extends Controller {
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
    if (!$this->auth->loggedIn())
      return $this->redirectToLoginPage();

    $this->response->setView('classes/new.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Create a New Class',
      'metaDescription' => "Create a new class so you can track the work of your students.",
      'activeLink' => 'teacher',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'submitButtonText' => 'Create'
    ));
    $this->addVar('errors');
    $this->addVar('formVars');
    $this->addVar('flash');
    return $this->response;
  }

  private function redirectToLoginPage() {
    $this->flash('You must be logged in to create a new class!', 'warning');
    $this->response->header('Location: /login');
    return $this->response;
  }
}
