<?php
namespace Tests\views\sketch;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class IndexHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicViewNoCollectionHtml() {
    $collections = [];
    $activeCollectionId = 0;
    $sketches = [
      [
        'sketch_id' => 1,
        'title' => 'funny-name',
        'updated_at' => '2021-07-13 18:51:30',
        'created_at' => '2021-07-10 12:00:00',
        'collection_id' => NULL
      ]
    ];
    $deletedSketches = [
      [
        'sketch_id' => 2,
        'title' => 'deleted-name',
        'deleted_at' => '2021-09-30 19:52:45'
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
      'sketches' => $sketches,
      'deletedSketches' => $deletedSketches,
      'collections' => $collections,
      'activeCollectionId' => $activeCollectionId
    ));
    $output = $response->requireView();
    $this->assertStringContainsString($pageTitle, $output);
    $this->assertStringContainsString($metaDescription, $output);

    $expect = '<form id="create-sketch-form" action="/sketch/create" method="POST" style="display: none;">';
    $this->assertStringContainsString($expect, $output);

    $expect = '<input type="hidden" name="collectionId" value="0" />';
    $this->assertStringContainsString($expect, $output);

    $expect = '<form id="new-collection-form" class="form-horizontal" action="/collection/create" method="POST" style="display: none;">';
    $this->assertStringContainsString($expect, $output);

    $expect = '<a href="/sketch" class="collection active">All Sketches</a>';
    $this->assertStringContainsString($expect, $output);

    $expect = '<h3><a href="/sketch/' . $sketches[0]['sketch_id'] . '">' . $sketches[0]['title'] . '</a></h3>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<form action="/sketch/' . $sketches[0]['sketch_id'] . '/delete" method="post">';
    $this->assertStringContainsString($expect, $output);

    $expect = '<h3>' . $deletedSketches[0]['title'] . '</h3>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<form action="/sketch/' . $deletedSketches[0]['sketch_id'] . '/restore" method="post">';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
