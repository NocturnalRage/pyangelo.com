<?php
namespace PyAngelo\Controllers\Sketch;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\SketchRepository;
use PyAngelo\Utilities\SketchFiles;
use Framework\{Request, Response};

class SketchSaveController extends Controller {
  protected $sketchRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    SketchRepository $sketchRepository,
    SketchFiles $sketchFiles
  ) {
    parent::__construct($request, $response, $auth);
    $this->sketchRepository = $sketchRepository;
    $this->sketchFiles = $sketchFiles;
  }

  public function exec() {
    $this->response->setView('sketch/saved.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Log in to save a sketch.'
      ));
      return $this->response;
    }

    // Validate the CRSF token
    if (!$this->auth->crsfTokenIsValid()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must update your sketch from the PyAngelo website.'
      ));
      return $this->response;
    }

    if (!isset($this->request->post['sketchId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a sketch to update.'
      ));
      return $this->response;
    }

    if (!isset($this->request->post['filename'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a filename to save.'
      ));
      return $this->response;
    }

    if (empty($this->request->post['program'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must have code to save.'
      ));
      return $this->response;
    }

    if (! $sketch = $this->sketchRepository->getSketchById($this->request->post['sketchId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a valid sketch to update.'
      ));
      return $this->response;
    }
    if ($sketch['person_id'] != $this->auth->personId()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must be the owner of the sketch to update it.'
      ));
      return $this->response;
    }
    $this->sketchFiles->saveCode(
      $sketch['sketch_id'],
      $this->request->post['filename'],
      $this->request->post['program']
    );

    $this->response->setVars(array(
        'status' => 'success',
        'message' => 'File saved.'
      ));
    return $this->response;
  }
}
