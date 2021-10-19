<?php
namespace Tests\views\collections;

use PHPUnit\Framework\TestCase;
use Framework\Response;

class CollectionsRenameJsonTest extends TestCase {

  public function testControllerRenameBasicView() {
    $collectionId = 5;
    $response = new Response('views');
    $response->setView('collections/rename.json.php');
    $response->setVars(array(
      'status' => "success",
      'message' => "file renamed",
      'title' => "Great Sketch",
      'collectionId' => $collectionId
    ));
    $output = $response->requireView();
    $expect = '"status":"success"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"message":"file renamed"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"title":"Great Sketch"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"collectionId":"' . $collectionId . '"';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
