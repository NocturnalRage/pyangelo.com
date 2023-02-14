<?php
namespace Tests\views\categories;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class OrderJsonTest extends BasicViewHtmlTestCase {

  public function testBasicView() {
    $response = new Response('views');
    $response->setView('categories/order.json.php');
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
