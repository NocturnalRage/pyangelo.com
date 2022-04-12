<?php
namespace PyAngelo\Controllers\Classes;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\ClassRepository;

class TeacherCreateController extends Controller {
  protected $classRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    ClassRepository $classRepository,
  ) {
    parent::__construct($request, $response, $auth);
    $this->classRepository = $classRepository;
  }

  public function exec() {
    if (! $this->auth->loggedIn())
      return $this->redirectToLoginPage();

    if (! $this->auth->crsfTokenIsValid())
      return $this->redirectToTeacherPageCrsf();

    if (empty($this->request->post['class_name'])) {
      $_SESSION['errors']['class_name'] = 'You must supply a name for your class.';
    }
    else if (strlen($this->request->post['class_name']) > 100) {
      $_SESSION['errors']['class_name'] = 'The class name must be no more than 100 characters.';
    }

    if (! empty($_SESSION['errors'])) {
      $this->flash('There were some errors. Please fix these below and then click the submit once more.', 'danger');
      $_SESSION['formVars'] = $this->request->post;
      $this->response->header('Location: /classes/teacher/new');
      return $this->response;
    }

    $classCode = bin2hex(random_bytes(16));

    $classId = $this->classRepository->createNewClass(
      $this->auth->personId(),
      $this->request->post['class_name'],
      $classCode
    );

    if (!$classId)
      return $this->redirectToTeacherPage();

    $header = "Location: /classes/teacher/" . $classId;
    $this->response->header($header);
    return $this->response;
  }

  private function redirectToLoginPage() {
    $this->flash("You must be logged in to create a class", "danger");
    $this->response->header('Location: /login');
    return $this->response;
  }

  private function redirectToTeacherPageCrsf() {
    $this->flash('You must create your class from the PyAngelo website.', 'danger');
    $this->response->header('Location: /classes/teacher');
    return $this->response;
  }

  private function redirectToTeacherPage() {
    $this->flash('Something went wrong and we could not create your class.', 'danger');
    $this->response->header('Location: /classes/teacher');
    return $this->response;
  }
}
