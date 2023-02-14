<?php
namespace Tests\views\categories;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class SortHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicViewWhenLoggedIn() {
    $category = [
      'category' => '3x3 Videos',
      'category_slug' => '3x3'
    ];
    $tutorials = [
      [
        'title' => 'Tutorial 1',
        'slug' => 'tutorial-1',
        'display_order' => 1,
        'thumbnail' => 'tutorial-1.jpg',
        'category' => '3x3 Videos',
        'category_slug' => '3x3'
      ],
      [
        'title' => 'Tutorial 2',
        'slug' => 'tutorial-2',
        'display_order' => 2,
        'thumbnail' => 'tutorial-2.jpg',
        'category' => '3x3 Videos',
        'category_slug' => '3x3'
      ],
    ];
    $response = new Response('views');
    $response->setView('categories/sort.html.php');
    $response->setVars(array(
      'pageTitle' => "Sort PyAngelo Tutorials",
      'metaDescription' => "Learn how to code.",
      'activeLink' => 'Tutorials',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'tutorials' => $tutorials,
      'category' => $category
    ));
    $output = $response->requireView();
    $expect = 'Sort PyAngelo Tutorials';
    $this->assertStringContainsString($expect, $output);
    $expect = 'Back to Tutorials';
    $this->assertStringContainsString($expect, $output);
    $expect = '<ul id="sortable" class="list-group">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<li id="tutorial-1" class=" list-group-item">Tutorial 1</li>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<li id="tutorial-2" class=" list-group-item">Tutorial 2</li>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<script src="https://code.jquery.com/jquery-1.12.4.js"></script>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<script src="/js/notify';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
