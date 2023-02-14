<?php
namespace Tests\views\sketch;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class CollectionNotOwnerHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicViewNotOwnerOfCollectionHtml() {
    $collection = [
        'collection_name' => 'My First Collection'
    ];
    $sketches = [
      [
        'sketch_id' => 1,
        'title' => 'funny-name',
        'updated_at' => '2021-07-13 18:51:30',
        'created_at' => '2021-07-10 12:00:00',
        'collection_id' => NULL
      ]
    ];
    $pageTitle = "PyAngelo - Programming Made Simple";
    $metaDescription = "Python in the browser";

    $response = new Response('views');
    $response->setView('sketch/collection-not-owner.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $metaDescription,
      'activeLink' => 'Home',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'sketches' => $sketches,
      'collection' => $collection
    ));
    $output = $response->requireView();
    $this->assertStringContainsString($pageTitle, $output);
    $this->assertStringContainsString($metaDescription, $output);

    $expect = '<h3><a href="/sketch/' . $sketches[0]['sketch_id'] . '">' . $sketches[0]['title'] . '</a></h3>';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
