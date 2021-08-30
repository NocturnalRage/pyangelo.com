<?php
namespace PyAngelo\Controllers\Sketch;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\SketchRepository;

class SketchDeleteController extends Controller {
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
      $this->flash('You must select a sketch to delete', 'danger');
      $this->response->header('Location: /sketch');
      return $this->response;
    }

    if (! $this->auth->loggedIn()) {
      $this->flash('You must be logged in to delete a sketch!', 'danger');
      $this->response->header('Location: /login');
      return $this->response;
    }
    if (! $this->auth->crsfTokenIsValid()) {
      $this->flash('Please delete sketches from the PyAngelo website!', 'danger');
      $this->response->header('Location: /sketch');
      return $this->response;
    }
    if (! $sketch = $this->sketchRepository->getSketchById($this->request->post['sketchId'])) {
      $this->flash('You must select a valid sketch to delete!', 'danger');
      $this->response->header('Location: /sketch');
      return $this->response;
    }
    if ($sketch['person_id'] != $this->auth->personId()) {
      $this->flash('You must be the owner of the sketch to delete it.', 'danger');
      $this->response->header('Location: /sketch');
      return $this->response;
    }

    $rowsUpdated = $this->sketchRepository->deleteSketch(
      $this->request->post['sketchId']
    );

    if ($rowsUpdated == 1) {
      $this->flash('Your sketch has been deleted.', 'success');
    }
    else {
      $this->flash('Sorry, we could not delete the sketch.', 'danger');
    }

    $this->response->header('Location: /sketch');
    return $this->response;
  }
}
