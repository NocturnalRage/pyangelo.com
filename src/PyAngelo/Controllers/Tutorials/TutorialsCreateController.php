<?php
namespace PyAngelo\Controllers\Tutorials;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\FormServices\TutorialFormService;

class TutorialsCreateController extends Controller {
  protected $tutorialFormService;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    TutorialFormService $tutorialFormService
  ) {
    parent::__construct($request, $response, $auth);
    $this->tutorialFormService = $tutorialFormService;
  }

  public function exec() {
    if (!$this->auth->isAdmin()) {
      return $this->redirectToHomePageWithWarning();
    }
    $success = $this->tutorialFormService->createTutorial(
      $this->request->post,
      $this->request->files['thumbnail'],
      $this->request->files['pdf']
    );
    if (!$success) {
      $_SESSION['errors'] = $this->tutorialFormService->getErrors();
      $this->flash($this->tutorialFormService->getFlashMessage(), 'danger');
      $_SESSION['formVars'] = $this->request->post;
      $this->response->header('Location: /tutorials/new');
      return $this->response;
    }
    $this->response->header('Location: /tutorials');
    return $this->response;
  }

  private function redirectToHomePageWithWarning() {
    $this->flash('You are not authorised!', 'danger');
    $this->response->header('Location: /');
    return $this->response;
  }
}
