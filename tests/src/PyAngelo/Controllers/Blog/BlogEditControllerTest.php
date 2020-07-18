<?php
namespace tests\src\PyAngelo\Controllers\Blog;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Blog\BlogEditController;

class BlogEditControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->blogRepository = Mockery::mock('PyAngelo\Repositories\BlogRepository');
    $this->controller = new BlogEditController (
      $this->request,
      $this->response,
      $this->auth,
      $this->blogRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Blog\BlogEditController');
  }

  public function testBlogEditControllerWhenNotAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testBlogEditControllerWhenAdminNoSlug() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testBlogEditControllerWhenAdminNoSuchBlog() {
    $slug = 'no-such-slug';
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->blogRepository->shouldReceive('getBlogBySlug')
      ->once()
      ->with($slug)
      ->andReturn(false);
    $this->request->get['slug'] = $slug;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  /**
   * @runInSeparateProcess
   */
  public function testBlogEditControllerWhenAdminExistingBlog() {
    session_start();
    $slug = 'existing-slug';
    $blog = [
      'title' => 'Great Blog',
      'slug' => $slug
    ];
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->blogRepository->shouldReceive('getBlogBySlug')
      ->once()
      ->with($slug)
      ->andReturn($blog);
    $this->blogRepository->shouldReceive('getAllBlogCategories')
      ->once()
      ->with()
      ->andReturn([]);
    $this->request->get['slug'] = $slug;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/edit.html.php';
    $expectedPageTitle = 'Edit ' . $blog['title'] . ' Blog';
    $expectedMetaDescription = "Edit this PyAngelo blog.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
