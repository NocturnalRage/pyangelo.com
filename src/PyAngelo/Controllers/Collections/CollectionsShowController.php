<?php
namespace PyAngelo\Controllers\Collections;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\SketchRepository;

class CollectionsShowController extends Controller {
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
    if (!isset($this->request->get['collectionId']))
      return $this->redirectToPageNotFound();

    if (!($collection = $this->sketchRepository->getCollectionById(
      $this->request->get['collectionId']
    )))
      return $this->redirectToPageNotFound();

    if ($collection['person_id'] != $this->auth->personId()) {
      return $this->redirectToHomePageWithWarning();
    }

    $collections = $this->sketchRepository->getCollections($this->auth->personId());
    $sketches = $this->sketchRepository->getCollectionSketches(
      $this->request->get['collectionId']
    );

    $this->response->setView('sketch/index.html.php');
    $this->response->setVars(array(
      'pageTitle' => $collection['collection_name'] . ' | PyAngelo',
      'metaDescription' => 'Your PyAngelo sketches stored in a collection named: ' . $collection['collection_name'],
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'sketches' => $sketches,
      'activeLink' => 'My Sketches',
      'collections' => $collections,
      'collection' => $collection,
      'activeCollectionId' => $this->request->get['collectionId']
    ));
    return $this->response;
  }

  private function redirectToPageNotFound() {
    $this->response->header('Location: /page-not-found');
    return $this->response;
  }

  private function redirectToHomePageWithWarning() {
    $this->flash('You can only view your own collections!', 'danger');
    $this->response->header('Location: /');
    return $this->response;
  }
}
