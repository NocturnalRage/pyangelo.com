<?php
namespace PyAngelo\Controllers\Collections;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\SketchRepository;

class CollectionsCreateController extends Controller {
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

    // Validate the CRSF token
    if (! $this->auth->crsfTokenIsValid()) {
      $this->flash('Please create collections from the PyAngelo website!', 'danger');
      $this->response->header('Location: /sketch');
      return $this->response;
    }

    if (!isset($this->request->post['collectionTitle']) || empty(trim($this->request->post['collectionTitle']))) {
      $this->flash('You must provide a name for your collection.', 'danger');
      $this->response->header('Location: /sketch');
      return $this->response;
    }

    $collectionId = $this->sketchRepository->createNewCollection(
      $this->auth->personId(),
      $this->request->post['collectionTitle']
    );

    if (!$collectionId) {
      $this->flash('Error! We could not create a new collection for you :(', 'danger');
    }

    $this->response->header('Location: /sketch');
    return $this->response;
  }

  private function redirectToLoginPage() {
    $this->flash("You must be logged in to create a new sketch", "danger");
    $this->response->header('Location: /login');
    return $this->response;
  }

  private function redirectToSketchPage() {
  }
}
