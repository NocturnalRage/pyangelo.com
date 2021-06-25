<?php
namespace Tests\views\reference;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class AssetLibraryHtmlTest extends BasicViewHtmlTest {

  public function testBasicViewHtml() {
    $pageTitle = "PyAngelo - Asset Library";
    $metaDescription = "Python Asset Library";

    $response = new Response('views');
    $response->setView('reference/asset-library.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $metaDescription,
      'activeLink' => 'Home',
      'personInfo' => $this->setPersonInfoLoggedIn()
    ));
    $output = $response->requireView();
    $this->assertStringContainsString($pageTitle, $output);
    $this->assertStringContainsString($metaDescription, $output);

    $expect = 'Asset Library';
    $this->assertStringContainsString($expect, $output);

    $expect = 'Images';
    $this->assertStringContainsString($expect, $output);

    $expect = 'Sounds';
    $this->assertStringContainsString($expect, $output);

    $expect = 'Music';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
