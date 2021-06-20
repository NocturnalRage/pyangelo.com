<?php
namespace Tests\views\sketch;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class ShowHtmlTest extends BasicViewHtmlTest {

  public function testBasicViewOwnsSketchHtml() {
    $personId = 101;
    $sketch = [
      'sketch_id' => 1,
      'person_id' => $personId,
      'title' => 'My Great Sketch'
    ];
    $sketchFiles = [
      [
        'file_id' => 1,
        'filename' => 'main.py'
      ]
    ];
    $pageTitle = "PyAngelo - Programming Made Simple";
    $metaDescription = "Python in the browser";
    $response = new Response('views');
    $response->setView('sketch/show.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $metaDescription,
      'activeLink' => 'Home',
      'personInfo' => $this->setPersonInfoLoggedIn($personId),
      'sketch' => $sketch,
      'sketchFiles' => $sketchFiles
    ));
    $output = $response->requireView();
    $this->assertStringContainsString($pageTitle, $output);
    $this->assertStringContainsString($metaDescription, $output);

    $expect = '<base href="/sketches/' . $sketch['person_id'] . '/' . $sketch['sketch_id'] . '/" />';
    $this->assertStringContainsString($expect, $output);

    $expect = $sketch['title'];
    $this->assertStringContainsString($expect, $output);
    $expect = '<canvas id="canvas" width="0" height="0" tabindex="1"></canvas>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<button id="startStop" class="btn btn-success">Start</button>';
    $this->assertStringContainsString($expect, $output);

    $expect = 'Fork this sketch';
    $this->assertStringNotContainsString($expect, $output);

    $expect = '<div id="editor"';
    $this->assertStringContainsString($expect, $output);

    $expect = '<pre id="console">';
    $this->assertStringContainsString($expect, $output);

    $expect = '<script src="/js/PyAngeloSetup.js';
    $this->assertStringContainsString($expect, $output);

    $expect = '<script src="/js/editor.js';
    $this->assertStringContainsString($expect, $output);
  }

  public function testBasicViewDoesNotOwnSketchHtml() {
    $personId = 101;
    $sketch = [
      'sketch_id' => 1,
      'person_id' => 50,
      'title' => 'My Great Sketch'
    ];
    $sketchFiles = [
      [
        'file_id' => 1,
        'filename' => 'main.py'
      ]
    ];
    $pageTitle = "PyAngelo - Programming Made Simple";
    $metaDescription = "Python in the browser";
    $response = new Response('views');
    $response->setView('sketch/show.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $metaDescription,
      'activeLink' => 'Home',
      'personInfo' => $this->setPersonInfoLoggedIn($personId),
      'sketch' => $sketch,
      'sketchFiles' => $sketchFiles
    ));
    $output = $response->requireView();
    $this->assertStringContainsString($pageTitle, $output);
    $this->assertStringContainsString($metaDescription, $output);

    $expect = '<h1 class="text-center">' . $sketch['title'] . '</h1>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<canvas id="canvas" width="0" height="0" tabindex="1"></canvas>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<button id="startStop" class="btn btn-success">Start</button>';
    $this->assertStringContainsString($expect, $output);

    $expect = 'Fork this sketch';
    $this->assertStringContainsString($expect, $output);

    $expect = '<div id="editor"';
    $this->assertStringContainsString($expect, $output);

    $expect = '<pre id="console">';
    $this->assertStringContainsString($expect, $output);

    $expect = '<script src="/js/PyAngeloSetup.js';
    $this->assertStringContainsString($expect, $output);

    $expect = '<script src="/js/editor.js';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
