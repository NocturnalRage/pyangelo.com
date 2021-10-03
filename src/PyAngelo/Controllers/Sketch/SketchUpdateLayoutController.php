<?php
namespace PyAngelo\Controllers\Sketch;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\SketchRepository;
use Framework\{Request, Response};

class SketchUpdateLayoutController extends Controller {
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
    $this->response->setView('sketch/layout.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Log in to update the layout of a sketch.'
      ));
      return $this->response;
    }

    // Validate the CRSF token
    if (!$this->auth->crsfTokenIsValid()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must update the layout of your sketch from the PyAngelo website.'
      ));
      return $this->response;
    }

    if (!isset($this->request->post['sketchId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a sketch to update the layout of.'
      ));
      return $this->response;
    }

    if (!isset($this->request->post['layout'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a layout to save.'
      ));
      return $this->response;
    }

    if (! $sketch = $this->sketchRepository->getSketchById($this->request->post['sketchId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a valid sketch to update the layout of.'
      ));
      return $this->response;
    }
    if ($sketch['person_id'] != $this->auth->personId()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must be the owner of the sketch to update the layout.'
      ));
      return $this->response;
    }
    $rowsUpdated = $this->sketchRepository->updateSketchLayout(
      $this->request->post['sketchId'],
      $this->request->post['layout']
    );

    if ($rowsUpdated != 1) {
      $this->response->setVars(array(
          'status' => 'error',
          'message' => 'Could not update the layout.'
        ));
      return $this->response;
    }

    $this->response->setVars(array(
        'status' => 'success',
        'message' => 'Layout updated.'
      ));
    return $this->response;
  }
}
