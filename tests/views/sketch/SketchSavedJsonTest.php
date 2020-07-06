<?php
namespace Tests\views\sketch;

use PHPUnit\Framework\TestCase;
use Framework\Response;

class SketchSavedJsonTest extends TestCase {

  public function testBasicView() {
    $response = new Response('views');
    $response->setView('sketch/saved.json.php');
    $response->setVars(array(
      'status' => "success",
      'message' => "file saved"
    ));
    $output = $response->requireView();
    $expect = '"status":"success"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"message":"file saved"';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
