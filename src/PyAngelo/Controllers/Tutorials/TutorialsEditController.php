<?php
namespace PyAngelo\Controllers\Tutorials;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;

class TutorialsEditController extends Controller {
  protected $tutorialRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    TutorialRepository $tutorialRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->tutorialRepository = $tutorialRepository;
  }

  public function exec() {
    if (!$this->auth->isAdmin())
      return $this->redirectToHomePageWithWarning();

    if (!isset($this->request->get['slug']))
      return $this->redirectToPageNotFound();

    if (!($tutorial = $this->tutorialRepository->getTutorialBySlug($this->request->get['slug'])))
      return $this->redirectToPageNotFound();

    $formVars = $this->request->session['formVars'] ?? $tutorial;
    unset($this->request->session['formVars']);

    $this->response->setView('tutorials/edit.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Edit ' . $tutorial['title'] . ' Tutorial',
      'metaDescription' => "Edit this PyAngelo tutorial.",
      'activeLink' => 'Tutorials',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'tutorial' => $tutorial,
      'categories' => $this->tutorialRepository->getAllTutorialCategories(),
      'levels' => $this->tutorialRepository->getAllTutorialLevels(),
      'formVars' => $formVars,
      'submitButtonText' => 'Update'
    ));
    $this->addVar('errors');
    $this->addVar('flash');
    return $this->response;
  }

  private function redirectToHomePageWithWarning() {
    $this->flash('You are not authorised!', 'danger');
    $this->response->header('Location: /');
    return $this->response;
  }

  private function redirectToPageNotFound() {
    $this->response->header('Location: /page-not-found');
    return $this->response;
  }
}
