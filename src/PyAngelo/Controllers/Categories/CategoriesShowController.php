<?php
namespace PyAngelo\Controllers\Categories;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;

class CategoriesShowController extends Controller {
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
    if (! isset($this->request->get['slug']))
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
    $this->response->setView('categories/show.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'PyAngelo Tutorials | ' . $tutorials[0]['category'],
      'metaDescription' => "Learn to code using Python graphics programming in the browser.",
      'activeLink' => 'Tutorials',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'tutorials' => $tutorials,
      'category' => $category
    ));
    return $this->response;
  }

  private function redirectToPageNotFound() {
    $this->response->header('Location: /page-not-found');
    return $this->response;
  }
}

