<?php
namespace PyAngelo\Controllers\Tutorials;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\FormServices\TutorialFormService;

class TutorialsUpdateController extends Controller {
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
    if (!$this->auth->isAdmin())
      return $this->redirectToLoginPageWithWarning();

    if (!isset($this->request->post['slug']))
      return $this->redirectToPageNotFound();

    $success = $this->tutorialFormService->updateTutorial(
      $this->request->post,
      $this->request->files['thumbnail'],
      $this->request->files['pdf']
    );
    if (!$success) {
      $_SESSION['errors'] = $this->tutorialFormService->getErrors();
      $this->flash($this->tutorialFormService->getFlashMessage(), 'danger');
      $_SESSION['formVars'] = $this->request->post;
      $location = 'Location: /tutorials/' . urlencode($this->request->post['slug']) . '/edit';
      $this->response->header($location);
      return $this->response;
    }
    $location = 'Location: /tutorials/' . urlencode($this->request->post['slug']);
    $this->response->header($location);
    return $this->response;
  }

  private function redirectToLoginPageWithWarning() {
    $this->flash('You are not authorised!', 'danger');
    $this->response->header('Location: /');
    return $this->response;
  }

  private function redirectToPageNotFound() {
    $this->response->header('Location: /page-not-found');
    return $this->response;
  }
}
