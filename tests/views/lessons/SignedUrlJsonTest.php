<?php
namespace Tests\views\lessons;

use PHPUnit\Framework\TestCase;
use Framework\Response;

class SignedUrlJsonTest extends TestCase {

  public function testBasicView() {
    $response = new Response('views');
    $response->setView('lessons/signed-url.json.php');
    $response->setVars(array(
      'status' => "success",
      'message' => "It worked.",
      'signedUrl' => "signed",
      'youtubeUrl' => "youtube",
    ));
    $output = $response->requireView();
    $expect = '"status":"success"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"message":"It worked."';
    $this->assertStringContainsString($expect, $output);
    $expect = '"signedUrl":"signed"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"youtubeUrl":"youtube"';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
