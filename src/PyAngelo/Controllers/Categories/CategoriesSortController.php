<?php
namespace PyAngelo\Controllers\Categories;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;

class CategoriesSortController extends Controller {
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
    if (! $this->auth->isAdmin())
      return $this->redirectToHomePageWithWarning();

    if (!isset($this->request->get['slug']))
      return $this->redirectToPageNotFound();

    if (!($tutorials = $this->tutorialRepository->getTutorialsByCategory(
      $this->request->get['slug']
    )))
      return $this->redirectToPageNotFound();

    $category = [
      'tutorial_category_id' => $tutorials[0]['tutorial_category_id'],
      'category' => $tutorials[0]['category'],
      'category_slug' => $tutorials[0]['category_slug']
    ];
    $this->response->setView('categories/sort.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Sort PyAngelo Tutorials',
      'metaDescription' => "A page where you can change the order PyAngelo tutorials are displayed in.",
      'activeLink' => 'Tutorials',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'tutorials' => $tutorials,
      'category' => $category
    ));
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
