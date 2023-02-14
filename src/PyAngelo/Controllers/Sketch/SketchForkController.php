<?php
namespace PyAngelo\Controllers\Sketch;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\SketchRepository;
use PyAngelo\Utilities\SketchFiles;

class SketchForkController extends Controller {
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

    if (! $this->auth->loggedIn())
      return $this->redirectToLoginPage();

    if (!$this->auth->crsfTokenIsValid())
      return $this->redirectToHomePage();

    if (!isset($this->request->post['sketchId']))
      return $this->redirectToHomePage();

    $origSketch = $this->sketchRepository->getSketchById(
      $this->request->post['sketchId']
    );

    if (! $origSketch)
      return $this->redirectToHomePage();

    $sketchId = $this->sketchRepository->forkSketch(
      $origSketch['sketch_id'],
      $this->auth->personId(),
      $origSketch['title'],
      NULL,
      NULL,
      $origSketch['layout']
    );

    if (!$sketchId)
      return $this->redirectToSketchPage();

    $sketchFiles = $this->sketchRepository->getSketchFiles($sketchId);
    $this->sketchFiles->forkSketch(
      $origSketch,
      $this->auth->personId(),
      $sketchId,
      $sketchFiles
    );

    $header = "Location: /sketch/" . $sketchId;
    $this->response->header($header);
    return $this->response;
  }

  private function redirectToLoginPage() {
    $this->flash("You must be logged in to fork a sketch", "danger");
    $this->response->header('Location: /login');
    return $this->response;
  }

  private function redirectToHomePage() {
    $this->flash("Sorry, we could not fork the sketch.", "danger");
    $this->response->header('Location: /');
    return $this->response;
  }

  private function redirectToSketchPage() {
    $this->flash('Error! We could not fork the sketch for you :(', 'danger');
    $this->response->header('Location: /sketch/' . $this->request->post['sketchId']);
    return $this->response;
  }
}
