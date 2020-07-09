<?php
namespace Tests\views\lessons;

use PHPUnit\Framework\TestCase;
use Framework\Response;

class ToggleVideoCompletedJsonTest extends TestCase {

  public function testBasicView() {
    $response = new Response('views');
    $response->setView('lessons/toggle-completed.json.php');
    $response->setVars(array(
      'status' => "success",
      'message' => "It worked.",
      'percentComplete' => 50
    ));
    $output = $response->requireView();
    $expect = '"status":"success"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"message":"It worked."';
    $this->assertStringContainsString($expect, $output);
    $expect = '"percentComplete":"50"';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
