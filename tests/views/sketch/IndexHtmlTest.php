<?php
namespace Tests\views\sketch;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class IndexHtmlTest extends BasicViewHtmlTest {

  public function testBasicViewHtml() {
    $sketches = [
      [
        'sketch_id' => 1,
        'title' => 'funny-name'
      ]
    ];
    $pageTitle = "PyAngelo - Programming Made Simple";
    $metaDescription = "Python in the browser";

    $response = new Response('views');
    $response->setView('sketch/index.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $metaDescription,
      'activeLink' => 'Home',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'sketches' => $sketches
    ));
    $output = $response->requireView();
    $this->assertStringContainsString($pageTitle, $output);
    $this->assertStringContainsString($metaDescription, $output);

    $expect = '<form id="create-sketch-form" action="/sketch/create" method="POST" style="display: none;">';
    $this->assertStringContainsString($expect, $output);

    $expect = '<h3><a href="/sketch/' . $sketches[0]['sketch_id'] . '">' . $sketches[0]['title'] . '</a></h3>';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
