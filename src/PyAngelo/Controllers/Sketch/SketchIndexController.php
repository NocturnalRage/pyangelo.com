<?php
namespace PyAngelo\Controllers\Sketch;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\SketchRepository;

class SketchIndexController extends Controller {
  protected $sketchRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    SketchRepository $sketchRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->sketchRepository = $sketchRepository;
  }

  public function exec() {
    if (! $this->auth->loggedIn())
      return $this->redirectToLoginPage();

    $allSketches = $this->sketchRepository->getSketches($this->auth->personId());
    $sketches = array_filter($allSketches, function($sketch) {
      return ! $sketch['deleted'];
    });
    $deletedSketches = array_filter($allSketches, function($sketch) {
      return $sketch['deleted'];
    });

    $collections = $this->sketchRepository->getCollections($this->auth->personId());

    $this->response->setView('sketch/index.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'My PyAngelo Sketches',
      'metaDescription' => "View all the great sketches you have been created on PyAngelo.",
      'activeLink' => 'My Sketches',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'sketches' => $sketches,
      'deletedSketches' => $deletedSketches,
      'collections' => $collections,
      'activeCollectionId' => 0
    ));
    $this->addVar('flash');
    return $this->response;
  }

  private function redirectToLoginPage() {
    $this->flash("You must be logged in to view your sketches", "danger");
    $this->response->header('Location: /login');
    return $this->response;
  }
}
