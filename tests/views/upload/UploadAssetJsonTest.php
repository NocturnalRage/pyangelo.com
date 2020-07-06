<?php
namespace Tests\views\upload;

use PHPUnit\Framework\TestCase;
use Framework\Response;

class UploadAssetJsonTest extends TestCase {

  public function testBasicView() {
    $response = new Response('views');
    $response->setView('upload/upload-asset.json.php');
    $response->setVars(array(
      'status' => "success",
      'message' => "It worked.",
      'filename' => "pyangelo.png",
      'filetype' => "png"
    ));
    $output = $response->requireView();
    $expect = '"status":"success"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"message":"It worked."';
    $this->assertStringContainsString($expect, $output);
    $expect = '"filename":"pyangelo.png"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"filetype":"png"';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
