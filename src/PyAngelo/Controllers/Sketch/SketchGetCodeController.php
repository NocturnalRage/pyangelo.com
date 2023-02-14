<?php
namespace PyAngelo\Controllers\Sketch;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\SketchRepository;

class SketchGetCodeController extends Controller {
  protected $sketchRepository;
  protected $appDir;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    SketchRepository $sketchRepository,
    string $appDir
  ) {
    parent::__construct($request, $response, $auth);
    $this->sketchRepository = $sketchRepository;
    $this->appDir = $appDir;
  }

  public function exec() {
    $this->response->setView('sketch/code.json.php');
    $this->response->header('Content-Type: application/json');

    if (!isset($this->request->get['sketchId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a sketch to fetch.'
      ));
      return $this->response;
    }
    if (!($sketchFiles = $this->sketchRepository->getSketchFiles(
      $this->request->get['sketchId']
    ))) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a valid sketch to fetch.'
      ));
      return $this->response;
    }

    for ($i = 0; $i < count($sketchFiles); $i++) {
      if ($this->endsWith($sketchFiles[$i]['filename'], ".py")) {
        $sketchFiles[$i]['sourceCode'] = $this->readCodeFromFile(
          $sketchFiles[$i]['person_id'],
          $sketchFiles[$i]['sketch_id'],
          $sketchFiles[$i]['filename']
        );
      }
    }

    $this->response->setVars(array(
        'status' => 'success',
        'message' => 'files retrieved',
        'sketchFiles' => $sketchFiles
      ));
    return $this->response;
  }

  function readCodeFromFile($personId, $sketchId, $programName) {
    $basePath = $this->appDir . '/public/sketches/' . $personId . '/' . $sketchId;
    $filename = $basePath . '/' . $programName;
    return @file_get_contents($filename);
  }

  function endsWith($haystack, $needle) {
    return substr_compare($haystack, $needle, -strlen($needle)) === 0;
  }
}
