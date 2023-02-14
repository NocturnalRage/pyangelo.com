<?php
namespace Tests\views\blog;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class IndexHtmlTest extends BasicViewHtmlTestCase {
  public function tearDown(): void {
    \Mockery::close();
  }

  public function testBasicViewWhenNotAdmin() {
    $blogs = [
      [
        'title' => 'Blog 1',
        'slug' => 'blog-1',
        'blog_image' => 'blog-image.jpg',
        'content' => 'Blog 1',
        'preview' => 'Preview 1',
        'category_description' => 'Coding Thoughts',
        'category_slug' => '3x3',
        'published_at' => '2017-01-01 12:30:00'
      ],
    ];
    $purifier = \Mockery::mock('Framework\Presentation\HtmlPurifierPurify');
    $purifier->shouldReceive('purify')
      ->twice()
      ->with($blogs[0]['preview'])
      ->andReturn($blogs[0]['preview']);
    $response = new Response('views');
    $response->setView('blog/index.html.php');
    $response->setVars(array(
      'pageTitle' => "PyAngelo Blog",
      'metaDescription' => "Fun blogs by PyAngelo.",
      'activeLink' => 'blog',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'blogs' => $blogs,
      'featuredBlogs' => $blogs,
      'purifier' => $purifier
    ));
    $output = $response->requireView();
    $expect = 'Featured Blogs';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h3>Blog 1</h3>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h4><span class="label label-coding-thoughts">Coding Thoughts</span></h4>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<div>Preview 1</div>';
    $this->assertStringContainsString($expect, $output);
    $this->assertStringNotContainsString('New Blog', $output);
  }

  public function testBasicViewWhenAdmin() {
    $blogs = [
      [
        'title' => 'Blog 1',
        'slug' => 'blog-1',
        'blog_image' => 'blog-image.jpg',
        'content' => 'Blog 1',
        'preview' => 'Preview 1',
        'category_description' => 'Coding Thoughts',
        'category_slug' => '3x3',
        'published_at' => '2017-01-01 12:30:00'
      ],
    ];
    $purifier = \Mockery::mock('Framework\Presentation\HtmlPurifierPurify');
    $purifier->shouldReceive('purify')
      ->twice()
      ->with($blogs[0]['preview'])
      ->andReturn($blogs[0]['preview']);
    $response = new Response('views');
    $response->setView('blog/index.html.php');
    $response->setVars(array(
      'pageTitle' => "PyAngelo Blog",
      'metaDescription' => "Fun blogs by PyAngelo.",
      'activeLink' => 'blog',
      'personInfo' => $this->setPersonInfoAdmin(),
      'blogs' => $blogs,
      'featuredBlogs' => $blogs,
      'purifier' => $purifier
    ));
    $output = $response->requireView();
    $expect = 'Featured Blogs';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h3>Blog 1</h3>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h4><span class="label label-coding-thoughts">Coding Thoughts</span></h4>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<div>Preview 1</div>';
    $this->assertStringContainsString($expect, $output);
    $expect = 'New Blog';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
