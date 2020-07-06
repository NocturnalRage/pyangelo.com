<?php
namespace Tests\views\sketch;

use PHPUnit\Framework\TestCase;
use Framework\Response;

class SketchAddJsonTest extends TestCase {

  public function testBasicView() {
    $response = new Response('views');
    $response->setView('sketch/add.json.php');
    $response->setVars(array(
      'status' => "success",
      'message' => "file added",
      'filename' => "main.py"
    ));
    $output = $response->requireView();
    $expect = '"status":"success"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"message":"file added"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"filename":"main.py"';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
