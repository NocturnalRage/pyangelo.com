<?php
namespace Tests\views\sketch;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class RunHtmlTest extends BasicViewHtmlTest {

  public function testBasicViewHtml() {
    $sketch = [
      'sketch_id' => 1,
      'person_id' => 50,
      'title' => 'My Great Sketch'
    ];
    $sourceCode = "canvas.background()";
    $pageTitle = "PyAngelo - Programming Made Simple";
    $metaDescription = "Python in the browser";
    $response = new Response('views');
    $response->setView('sketch/run.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $metaDescription,
      'activeLink' => 'Home',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'sketch' => $sketch,
      'sourceCode' => $sourceCode
    ));
    $output = $response->requireView();
    $this->assertStringContainsString($pageTitle, $output);
    $this->assertStringContainsString($metaDescription, $output);

    $expect = $sketch['title'];
    $this->assertStringContainsString($expect, $output);
    $expect = '<canvas id="canvas" width="500" height="400" tabindex="1"></canvas>';
    $this->assertStringContainsString($expect, $output);

    $expect = '<div id="editor">' . $sourceCode . '</div>';
    $this->assertStringNotContainsString($expect, $output);

    $expect = '<div id="console">';
    $this->assertStringNotContainsString($expect, $output);
    $expect = 'import pyangelo';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
