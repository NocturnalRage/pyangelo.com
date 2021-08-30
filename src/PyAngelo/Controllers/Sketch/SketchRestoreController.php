<?php
namespace PyAngelo\Controllers\Sketch;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\SketchRepository;

class SketchRestoreController extends Controller {
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
    if (empty($this->request->post['sketchId'])) {
      $this->flash('You must select a sketch to restore', 'danger');
      $this->response->header('Location: /sketch');
      return $this->response;
    }

    if (! $this->auth->loggedIn()) {
      $this->flash('You must be logged in to restore a sketch!', 'danger');
      $this->response->header('Location: /login');
      return $this->response;
    }
    if (! $this->auth->crsfTokenIsValid()) {
      $this->flash('Please restore sketches from the PyAngelo website!', 'danger');
      $this->response->header('Location: /sketch');
      return $this->response;
    }
    if (! $sketch = $this->sketchRepository->getDeletedSketchById($this->request->post['sketchId'])) {
      $this->flash('You must select a valid sketch to restore!', 'danger');
      $this->response->header('Location: /sketch');
      return $this->response;
    }
    if ($sketch['person_id'] != $this->auth->personId()) {
      $this->flash('You must be the owner of the sketch to restore it.', 'danger');
      $this->response->header('Location: /sketch');
      return $this->response;
    }

    $rowsUpdated = $this->sketchRepository->restoreSketch(
      $this->request->post['sketchId']
    );

    if ($rowsUpdated == 1) {
      $this->flash('Your sketch has been restored.', 'success');
    }
    else {
      $this->flash('Sorry, we could not restore the sketch.', 'danger');
    }

    $this->response->header('Location: /sketch');
    return $this->response;
  }
}
