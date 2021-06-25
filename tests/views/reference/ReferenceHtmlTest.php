<?php
namespace Tests\views\reference;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class ReferenceHtmlTest extends BasicViewHtmlTest {

  public function testBasicViewHtml() {
    $pageTitle = "PyAngelo - Reference";
    $metaDescription = "Python Reference";

    $response = new Response('views');
    $response->setView('reference/reference.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $metaDescription,
      'activeLink' => 'Home',
      'personInfo' => $this->setPersonInfoLoggedIn()
    ));
    $output = $response->requireView();
    $this->assertStringContainsString($pageTitle, $output);
    $this->assertStringContainsString($metaDescription, $output);

    $expect = 'Reference';
    $this->assertStringContainsString($expect, $output);

    $expect = 'Canvas';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
