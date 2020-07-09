<?php
namespace Tests\views\lessons;

use PHPUnit\Framework\TestCase;
use Framework\Response;

class ToggleVideoFavouritedJsonTest extends TestCase {

  public function testBasicView() {
    $response = new Response('views');
    $response->setView('lessons/toggle-favourited.json.php');
    $response->setVars(array(
      'status' => "success",
      'message' => "It worked."
    ));
    $output = $response->requireView();
    $expect = '"status":"success"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"message":"It worked."';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
