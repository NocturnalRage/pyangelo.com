<?php
namespace PyAngelo\Controllers\Sketch;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\SketchRepository;

class SketchCanvasOnlyController extends Controller {
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
    if (!isset($this->request->get['sketchId']))
      return $this->redirectToPageNotFound();

    if (!($sketch = $this->sketchRepository->getSketchById(
      $this->request->get['sketchId']
    )))
      return $this->redirectToPageNotFound();

    $this->response->setView('sketch/canvasonly.html.php'); $this->response->setVars(array( 'pageTitle' => $sketch['title'] . ' | PyAngelo', 'metaDescription' => 'Another Wonderful PyAngelo Sketch',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'activeLink' => 'My Sketches',
      'sketch' => $sketch
    ));
    return $this->response;
  }

  private function redirectToPageNotFound() {
    $this->response->header('Location: /page-not-found');
    return $this->response;
  }
}
