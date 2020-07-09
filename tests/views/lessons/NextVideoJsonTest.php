<?php
namespace Tests\views\lessons;

use PHPUnit\Framework\TestCase;
use Framework\Response;

class NextVideoJsonTest extends TestCase {

  public function testBasicView() {
    $response = new Response('views');
    $response->setView('lessons/next-video.json.php');
    $response->setVars(array(
      'status' => "success",
      'message' => "It worked.",
      'lessonTitle' => "A great lesson",
      'tutorialSlug' => "a-tutorial",
      'lessonSlug' => "a-great-lesson"
    ));
    $output = $response->requireView();
    $expect = '"status":"success"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"message":"It worked."';
    $this->assertStringContainsString($expect, $output);
    $expect = '"lessonTitle":"A great lesson"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"tutorialSlug":"a-tutorial"';
    $this->assertStringContainsString($expect, $output);
    $expect = '"lessonSlug":"a-great-lesson"';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
