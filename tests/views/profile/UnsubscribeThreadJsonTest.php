<?php
namespace Tests\views\lessons;

use PHPUnit\Framework\TestCase;
use Framework\Response;

class UnsubscribeThreadJsonTest extends TestCase {

  public function testBasicView() {
    $response = new Response('views');
    $response->setView('profile/unsubscribe-thread.json.php');
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
