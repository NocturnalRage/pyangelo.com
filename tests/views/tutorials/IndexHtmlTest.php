<?php
namespace Tests\views\tutorials;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class IndexHtmlTest extends BasicViewHtmlTestCase {
  public function tearDown(): void {
    \Mockery::close();
  }

  public function testBasicViewWhenNotLoggedIn() {
    $tutorials = [
      [
        'title' => 'Tutorial 1',
        'slug' => 'tutorial-1',
        'thumbnail' => 'tutorial-1.jpg',
        'level' => 'Beginner',
        'category' => '3x3 Videos',
        'category_slug' => '3x3'
      ],
    ];
    $response = new Response('views');
    $response->setView('tutorials/index.html.php');
    $response->setVars(array(
      'pageTitle' => "PyAngelo Tutorials",
      'metaDescription' => "Learn to code.",
      'activeLink' => 'Tutorials',
      'personInfo' => $this->setPersonInfoLoggedOut(),
      'tutorials' => $tutorials
    ));
    $output = $response->requireView();
    $expect = 'PyAngelo Tutorials';
    $this->assertStringContainsString($expect, $output);
    $expect = '<img src="/uploads/images/tutorials/tutorial-1.jpg"';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h4><span class="label label-beginner">Beginner</span></h4>';
    $this->assertStringContainsString($expect, $output);
    $expect = 'alt="Tutorial 1" class="img-responsive featuredThumbnail"';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/tutorials/new" class="btn btn-warning">';
    $this->assertStringNotContainsString($expect, $output);
  }

  public function testBasicViewWhenLoggedInAsAdmin() {
    $tutorials = [
      [
        'title' => 'Tutorial 1',
        'slug' => 'tutorial-1',
        'thumbnail' => 'tutorial-1.jpg',
        'level' => 'Beginner',
        'category' => '3x3 Videos',
        'category_slug' => '3x3'
      ],
    ];
    $response = new Response('views');
    $response->setView('tutorials/index.html.php');
    $response->setVars(array(
      'pageTitle' => "PyAngelo Tutorials",
      'metaDescription' => "Learn to code.",
      'activeLink' => 'Tutorials',
      'personInfo' => $this->setPersonInfoAdmin(),
      'tutorials' => $tutorials
    ));
    $output = $response->requireView();
    $expect = 'PyAngelo Tutorials';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h4><span class="label label-beginner">Beginner</span></h4>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<img src="/uploads/images/tutorials/tutorial-1.jpg"';
    $this->assertStringContainsString($expect, $output);
    $expect = 'alt="Tutorial 1" class="img-responsive featuredThumbnail"';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/tutorials/new" class="btn btn-warning">';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
