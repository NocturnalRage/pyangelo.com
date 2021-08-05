<?php
// Lol I copied SketchAddFileController.php. Same thing really but different...?

namespace PyAngelo\Controllers\Sketch;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\SketchRepository;
use PyAngelo\Utilities\SketchFiles;
use Framework\{Request, Response};

class SketchDeleteFileController extends Controller {
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
    $this->response->setView('sketch/add.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Log in to delete a file from a sketch.',
        'filename' => 'File not deleted'
      ));
      return $this->response;
    }

    // Validate the CRSF token
    if (!$this->auth->crsfTokenIsValid()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must delete a file from the PyAngelo website. No CORS muahahaha',
        'filename' => 'File not deleted'
      ));
      return $this->response;
    }

    if (!isset($this->request->post['sketchId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a sketch to delete a file from.',
        'filename' => 'File not deleted'
      ));
      return $this->response;
    }

    if (!isset($this->request->post['filename'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a file to delete.',
        'filename' => 'File not deleted'
      ));
      return $this->response;
    }

    if (! $sketch = $this->sketchRepository->getSketchById($this->request->post['sketchId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a valid sketch to delete a file from.',
        'filename' => 'File not deleted'
      ));
      return $this->response;
    }
    if ($sketch['person_id'] != $this->auth->personId()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must be the owner of the sketch to delete a file from it.',
        'filename' => 'File not delete'
      ));
      return $this->response;
    }
    
    // Wow 83 lines just to verify the request... kinda hardcore imo
    // *when you scroll through a 100 line code and find that the actual stuff is does in 10 lines*
    $this->sketchRepository->deleteSketchFile(
      $sketch['sketch_id'],
      $this->request->post['filename']
    );

    $this->sketchFiles->deleteFile(
      $sketch,
      $this->request->post['filename'],
    );

    $this->response->setVars(array(
        'status' => 'success',
        'message' => 'File deleted.',
        'filename' => $this->request->post['filename']
      ));
    return $this->response;
  }
}
