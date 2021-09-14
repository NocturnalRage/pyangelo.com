<?php
namespace Tests\views\sketch;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class PlaygroundHtmlTest extends BasicViewHtmlTest {

  public function testBasicViewPlayground() {
    $pageTitle = "PyAngelo Playground";
    $metaDescription = "Python in the browser";
    $response = new Response('views');
    $response->setView('sketch/playground.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $metaDescription,
      'activeLink' => 'Home',
      'personInfo' => $this->setPersonInfoLoggedIn()
    ));
    $output = $response->requireView();
    $this->assertStringContainsString($pageTitle, $output);
    $this->assertStringContainsString($metaDescription, $output);

    $expect = 'PyAngelo Playground';
    $this->assertStringContainsString($expect, $output);
    $expect = '<canvas id="canvas" width="0" height="0" tabindex="1"></canvas>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<button id="startStop" class="btn btn-success">Start</button>';
    $this->assertStringContainsString($expect, $output);

    $expect = 'Fork this sketch';
    $this->assertStringNotContainsString($expect, $output);

    $expect = '<div id="editor"';
    $this->assertStringContainsString($expect, $output);

    $expect = '<pre id="console">';
    $this->assertStringContainsString($expect, $output);

    $expect = '<script src="/js/playground.js';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
