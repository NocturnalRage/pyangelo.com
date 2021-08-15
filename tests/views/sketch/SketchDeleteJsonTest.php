<?php
namespace Tests\views\sketch;

use PHPUnit\Framework\TestCase;
use Framework\Response;

class SketchDeleteJsonTest extends TestCase {

  public function testBasicView() {
    $response = new Response('views');
    $response->setView('sketch/delete.json.php');
    $response->setVars(array(
      'status' => "success",
      'message' => "file deleted",
      'filename' => "main.py"
    ));
    $output = $response->requireView();
    $expect = '"status":"success"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"message":"file deleted"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"filename":"main.py"';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
