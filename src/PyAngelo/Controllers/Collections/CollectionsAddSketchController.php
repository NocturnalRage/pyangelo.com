<?php
namespace PyAngelo\Controllers\Collections;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\SketchRepository;
use Framework\{Request, Response};

class CollectionsAddSketchController extends Controller {
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
    $this->response->setView('collections/add-sketch.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Log in to add a sketch to a collection.'
      ));
      return $this->response;
    }

    // Validate the CRSF token
    if (!$this->auth->crsfTokenIsValid()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must add a sketch to a collection from the PyAngelo website.'
      ));
      return $this->response;
    }

    if (!isset($this->request->post['sketchId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a sketch to add to your collection.'
      ));
      return $this->response;
    }

    if (! $sketch = $this->sketchRepository->getSketchById($this->request->post['sketchId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a valid sketch to add to your collection.'
      ));
      return $this->response;
    }

    if ($sketch['person_id'] != $this->auth->personId()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must be the owner of the sketch to add it to your collection.'
      ));
      return $this->response;
    }

    if (!isset($this->request->post['collectionId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a collection for your sketch.'
      ));
      return $this->response;
    }

    if ($this->request->post['collectionId'] == 0) {
      $rowsUpdated = $this->sketchRepository->removeSketchFromAllCollections(
        $this->request->post['sketchId']
      );
      if ($rowsUpdated != 1) {
        $this->response->setVars(array(
          'status' => 'error',
          'message' => 'Sorry, we could not remove your sketch from a collection.'
        ));
        return $this->response;
      }
      $this->response->setVars(array(
          'status' => 'removed',
          'message' => 'Sketch removed from collection'
        ));
      return $this->response;
    }
    else {
      $rowsUpdated = $this->sketchRepository->addSketchToCollection(
        $this->request->post['sketchId'],
        $this->request->post['collectionId']
      );

      if ($rowsUpdated != 1) {
        $this->response->setVars(array(
          'status' => 'error',
          'message' => 'Sorry, we could not add your sketcch to your collection.'
        ));
        return $this->response;
      }
      $this->response->setVars(array(
          'status' => 'added',
          'message' => 'Sketch added to collection'
        ));
      return $this->response;
    }


  }
}
