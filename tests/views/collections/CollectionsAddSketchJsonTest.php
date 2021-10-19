<?php
namespace Tests\views\collections;

use PHPUnit\Framework\TestCase;
use Framework\Response;

class CollectionsAddSketchJsonTest extends TestCase {

  public function testBasicCollectionsAddSketchView() {
    $response = new Response('views');
    $response->setView('collections/add-sketch.json.php');
    $response->setVars(array(
      'status' => "success",
      'message' => "Sketch added to collection"
    ));
    $output = $response->requireView();
    $expect = '"status":"success"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"message":"Sketch added to collection"';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
