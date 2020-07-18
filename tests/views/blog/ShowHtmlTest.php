<?php
namespace Tests\views\blog;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class ShowHtmlTest extends BasicViewHtmlTest {
  public function tearDown(): void {
    \Mockery::close();
  }

  public function testBasicViewWhenNotAdmin() {
    $blog = [
      'blog_id' => 1,
      'title' => 'Blog 1',
      'slug' => 'blog-1',
      'content' => 'Content 1',
      'preview' => 'Preview 1',
      'category_description' => 'Speedcubing Thoughts',
      'category_slug' => '3x3',
      'published_at' => '2017-01-01 12:30:00'
    ];
    $comments = [];
    $alertUser = false;
    $purifier = \Mockery::mock('Framework\Presentation\HtmlPurifierPurify');
    $purifier->shouldReceive('purify')
      ->once()
      ->with($blog['content'])
      ->andReturn($blog['content']);
    $avatar = \Mockery::mock('Framework\Presentation\Gravatar');
    $response = new Response('views');
    $response->setView('blog/show.html.php');
    $response->setVars(array(
      'pageTitle' => "CubeSkills Blog",
      'metaDescription' => "Fun blogs by CubeSkills.",
      'activeLink' => 'blog',
      'personInfo' => $this->setPersonInfoLoggedOut(),
      'blog' => $blog,
      'alertUser' => $alertUser,
      'purifier' => $purifier,
      'avatar' => $avatar,
      'comments' => $comments
    ));
    $alertUser = false;
    $output = $response->requireView();
    $expect = '<h1 class="text-center">Blog 1</h1>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h4><span class="label label-speedcubing-thoughts">Speedcubing Thoughts</span></h4>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<div>Content 1</div>';
    $this->assertStringContainsString($expect, $output);
    $this->assertStringNotContainsString('Edit Blog', $output);
  }

  public function testBasicViewWhenAdmin() {
    $blog = [
      'blog_id' => 1,
      'title' => 'Blog 1',
      'slug' => 'blog-1',
      'content' => 'Content 1',
      'preview' => 'Preview 1',
      'category_description' => 'Speedcubing Thoughts',
      'category_slug' => '3x3',
      'published_at' => '2017-01-01 12:30:00'
    ];
    $comments = [];
    $alertUser = false;
    $purifier = \Mockery::mock('Framework\Presentation\HtmlPurifierPurify');
    $purifier->shouldReceive('purify')
      ->once()
      ->with($blog['content'])
      ->andReturn($blog['content']);
    $avatar = \Mockery::mock('Framework\Presentation\Gravatar');
    $response = new Response('views');
    $response->setView('blog/show.html.php');
    $response->setVars(array(
      'pageTitle' => "CubeSkills Blog",
      'metaDescription' => "Fun blogs by CubeSkills.",
      'activeLink' => 'blog',
      'personInfo' => $this->setPersonInfoAdmin(),
      'blog' => $blog,
      'alertUser' => $alertUser,
      'purifier' => $purifier,
      'avatar' => $avatar,
      'comments' => $comments
    ));
    $output = $response->requireView();
    $expect = '<h1 class="text-center">Blog 1</h1>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h4><span class="label label-speedcubing-thoughts">Speedcubing Thoughts</span></h4>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<div>Content 1</div>';
    $this->assertStringContainsString($expect, $output);
    $expect = 'Edit Blog';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
