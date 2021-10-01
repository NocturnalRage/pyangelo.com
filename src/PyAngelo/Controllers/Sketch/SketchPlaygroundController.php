<?php
namespace PyAngelo\Controllers\Sketch;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class SketchPlaygroundController extends Controller {
  public function exec() {
    $sketch = [
      'sketch_id' => 0,
      'person_id' => 0
    ];
    $this->response->setView('sketch/playground.html.php'); $this->response->setVars(array( 'pageTitle' => 'PyAngelo Playground', 'metaDescription' => 'The PyAngelo playground lets you code without needing an account. You can experiment by coding on this page but you cannot save your work. For this you need to create an account. You also are not able to upload sound files or images via the playground so we encourage you to create your free account.',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'activeLink' => 'My Sketches',
      'sketch' => $sketch,
      'layout' => 'rows'
    ));
    return $this->response;
  }
}
