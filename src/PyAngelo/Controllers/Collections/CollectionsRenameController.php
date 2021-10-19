<?php
namespace PyAngelo\Controllers\Collections;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\SketchRepository;
use Framework\{Request, Response};

class CollectionsRenameController extends Controller {
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
    $this->response->setView('collections/rename.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Log in to rename a collection.',
        'title' => 'Unchanged.',
        'collectionId' => '0.'
      ));
      return $this->response;
    }

    // Validate the CRSF token
    if (!$this->auth->crsfTokenIsValid()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must rename your collection from the PyAngelo website.',
        'title' => 'Unchanged.',
        'collectionId' => '0.'
      ));
      return $this->response;
    }

    if (!isset($this->request->post['collectionId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a collection to rename.',
        'title' => 'Unchanged.',
        'collectionId' => '0.'
      ));
      return $this->response;
    }

    if (!isset($this->request->post['newTitle']) || empty(trim($this->request->post['newTitle']))) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must give your collection a title.',
        'title' => 'Unchanged.',
        'collectionId' => '0.'
      ));
      return $this->response;
    }

    if (! $collection = $this->sketchRepository->getCollectionById($this->request->post['collectionId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a valid collection to rename.',
        'title' => 'Unchanged.',
        'collectionId' => '0.'
      ));
      return $this->response;
    }

    if ($collection['person_id'] != $this->auth->personId()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must be the owner of the collection to rename it.',
        'title' => 'Unchanged.',
        'collectionId' => $this->request->post['collectionId']
      ));
      return $this->response;
    }
    $this->sketchRepository->renameCollection(
      $collection['collection_id'],
      $this->request->post['newTitle']
    );

    $this->response->setVars(array(
        'status' => 'success',
        'message' => 'Collection renamed.',
        'title' => $this->request->post['newTitle'],
        'collectionId' => $this->request->post['collectionId']
      ));
    return $this->response;
  }
}
