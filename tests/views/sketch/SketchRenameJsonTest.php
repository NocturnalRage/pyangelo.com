<?php
namespace Tests\views\sketch;

use PHPUnit\Framework\TestCase;
use Framework\Response;

class SketchRenameJsonTest extends TestCase {

  public function testBasicView() {
    $response = new Response('views');
    $response->setView('sketch/rename.json.php');
    $response->setVars(array(
      'status' => "success",
      'message' => "file renamed",
      'title' => "Great Sketch"
    ));
    $output = $response->requireView();
    $expect = '"status":"success"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"message":"file renamed"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"title":"Great Sketch"';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
