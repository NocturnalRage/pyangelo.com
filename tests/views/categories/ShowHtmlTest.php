<?php
namespace Tests\views\categories;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class ShowHtmlTest extends BasicViewHtmlTest {

  public function testBasicViewWhenNotLoggedIn() {
    $pageTitle = '3x3 Tutorials';
    $description = 'Great Tutorials';
    $tutorials = [
      [
        'title' => 'Tutorial 1',
        'slug' => 'tutorial-1',
        'thumbnail' => 'tutorial-1.jpg',
        'level' => 'Beginner',
        'tutorial_category_id' => 1,
        'category' => '3x3 Videos',
        'category_slug' => '3x3'
      ],
    ];
    $category = [
      'tutorial_category_id' => $tutorials[0]['tutorial_category_id'],
      'category' => $tutorials[0]['category'],
      'category_slug' => $tutorials[0]['category_slug']
    ];
    $response = new Response('views');
    $response->setView('categories/show.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $description,
      'activeLink' => 'Tutorials',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'tutorials' => $tutorials,
      'category' => $category
    ));
    $output = $response->requireView();
    $expect = '<img src="/uploads/images/tutorials/tutorial-1.jpg"';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h4><span class="label label-beginner">Beginner</span></h4>';
    $this->assertStringContainsString($expect, $output);
    $expect = 'alt="Tutorial 1" class="img-responsive featuredThumbnail"';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/tutorials/new" class="btn btn-warning">';
    $this->assertStringNotContainsString($expect, $output);
  }
}
?>
