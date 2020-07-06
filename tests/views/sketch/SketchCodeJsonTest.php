<?php
namespace Tests\views\sketch;

use PHPUnit\Framework\TestCase;
use Framework\Response;

class SketchCodeJsonTest extends TestCase {

  public function testBasicView() {
    $response = new Response('views');
    $response->setView('sketch/code.json.php');
    $sketchFiles = [
      [
        'filename' => 'main.py',
        'sourceCode' => 'canvas.clear()'
      ]
    ];
    $response->setVars(array(
      'status' => "success",
      'message' => "files retrieved",
      'sketchFiles' => $sketchFiles
    ));
    $output = $response->requireView();
    $expect = '"status":"success"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"message":"files retrieved"';
    $this->assertStringContainsString($expect, $output);
    $expect = 'files": [{"filename":"main.py","sourceCode":"canvas.clear()"}]';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
