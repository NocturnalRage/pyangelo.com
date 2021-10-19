<?php
namespace PyAngelo\Controllers\Sketch;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\SketchRepository;
use PyAngelo\Utilities\SketchFiles;
use Framework\{Request, Response};

class SketchRenameController extends Controller {
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
    $this->response->setView('sketch/rename.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Log in to rename a sketch.',
        'title' => 'Unchanged.'
      ));
      return $this->response;
    }

    // Validate the CRSF token
    if (!$this->auth->crsfTokenIsValid()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must rename your sketch from the PyAngelo website.',
        'title' => 'Unchanged.'
      ));
      return $this->response;
    }

    if (!isset($this->request->post['sketchId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a sketch to rename.',
        'title' => 'Unchanged.'
      ));
      return $this->response;
    }

    if (!isset($this->request->post['newTitle']) || empty(trim($this->request->post['newTitle']))) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must give your sketch a title.',
        'title' => 'Unchanged.'
      ));
      return $this->response;
    }

    if (! $sketch = $this->sketchRepository->getSketchById($this->request->post['sketchId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a valid sketch to rename.',
        'title' => 'Unchanged.'
      ));
      return $this->response;
    }

    if ($sketch['person_id'] != $this->auth->personId()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must be the owner of the sketch to rename it.',
        'title' => 'Unchanged.'
      ));
      return $this->response;
    }
    $this->sketchRepository->renameSketch(
      $sketch['sketch_id'],
      $this->request->post['newTitle']
    );

    $this->response->setVars(array(
        'status' => 'success',
        'message' => 'File renamed.',
        'title' => $this->request->post['newTitle']
      ));
    return $this->response;
  }
}
