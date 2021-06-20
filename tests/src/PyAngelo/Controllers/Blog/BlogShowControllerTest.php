<?php
namespace Tests\src\PyAngelo\Controllers\Blog;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Blog\BlogShowController;

class BlogShowControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->blogRepository = Mockery::mock('PyAngelo\Repositories\BlogRepository');
    $this->purifier = Mockery::mock('Framework\Contracts\PurifyContract');
    $this->avatar = Mockery::mock('Framework\Contracts\AvatarContract');
    $this->showCommentCount = 5;
    $this->controller = new BlogShowController (
      $this->request,
      $this->response,
      $this->auth,
      $this->blogRepository,
      $this->purifier,
      $this->avatar,
      $this->showCommentCount
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Blog\BlogShowController');
  }

  public function testBlogShowControllerWithNoSlug() {
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testBlogShowControllerWithInvalidSlug() {
    $slug = 'no-such-slug';
    $this->blogRepository->shouldReceive('getBlogBySlug')
      ->once()
      ->with($slug)
      ->andReturn(NULL);
    $this->request->get['slug'] = $slug;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testBlogShowControllerWithValidSlug() {
    $personId = 99;
    $comments = [];
    $blogId = 1;
    $slug = 'valid-slug';
    $blog = [
      'blog_id' => $blogId,
      'title' => 'Great Blog',
      'preview' => 'A great blog post.',
      'slug' => $slug
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(TRUE);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->blogRepository->shouldReceive('getBlogBySlug')
      ->once()
      ->with($slug)
      ->andReturn($blog);
    $this->blogRepository->shouldReceive('getPublishedBlogComments')
      ->once()
      ->with($blogId)
      ->andReturn($comments);
    $this->blogRepository->shouldReceive('shouldUserReceiveAlert')
      ->once()
      ->with($blogId, $personId)
      ->andReturn(FALSE);
    $this->request->get['slug'] = $slug;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/show.html.php';
    $expectedPageTitle = $blog['title'];
    $expectedMetaDescription = $blog['preview'];
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
