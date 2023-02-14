<?php
namespace PyAngelo\Controllers\Sketch;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\SketchRepository;
use PyAngelo\Utilities\SketchFiles;
use Framework\{Request, Response};

class SketchAddFileController extends Controller {
  protected $sketchRepository;
  protected $sketchFiles;

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
    $this->response->setView('sketch/add.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Log in to add a file to a sketch.',
        'filename' => 'File not created'
      ));
      return $this->response;
    }

    // Validate the CRSF token
    if (!$this->auth->crsfTokenIsValid()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must add a file from the PyAngelo website.',
        'filename' => 'File not created'
      ));
      return $this->response;
    }

    if (!isset($this->request->post['sketchId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a sketch to add a file to.',
        'filename' => 'File not created'
      ));
      return $this->response;
    }

    if (!isset($this->request->post['filename'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a filename to add.',
        'filename' => 'File not created'
      ));
      return $this->response;
    }

    if (! $sketch = $this->sketchRepository->getSketchById($this->request->post['sketchId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a valid sketch to add a file to.',
        'filename' => 'File not created'
      ));
      return $this->response;
    }
    if ($sketch['person_id'] != $this->auth->personId()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must be the owner of the sketch to add a file to it.',
        'filename' => 'File not created'
      ));
      return $this->response;
    }

    $this->sketchRepository->addSketchFile(
      $sketch['sketch_id'],
      $this->request->post['filename']
    );

    $this->sketchFiles->createFile(
      $sketch,
      $this->request->post['filename'],
    );

    $this->response->setVars(array(
        'status' => 'success',
        'message' => 'File saved.',
        'filename' => $this->request->post['filename']
      ));
    return $this->response;
  }
}
