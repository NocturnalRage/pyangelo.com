<?php
namespace tests\src\PyAngelo\Controllers\Blog;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Blog\BlogIndexController;

class BlogIndexControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->blogRepository = Mockery::mock('PyAngelo\Repositories\BlogRepository');
    $this->purifier = Mockery::mock('Framework\Contracts\PurifyContract');
    $this->controller = new BlogIndexController (
      $this->request,
      $this->response,
      $this->auth,
      $this->blogRepository,
      $this->purifier
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Blog\BlogIndexController');
  }


  /**
   * @runInSeparateProcess
   */
  public function testBlogIndexController() {
    session_start();
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->blogRepository->shouldReceive('getAllBlogs')
      ->once()
      ->with()
      ->andReturn([]);
    $this->blogRepository->shouldReceive('getFeaturedBlogs')
      ->once()
      ->with()
      ->andReturn([]);
    $this->request->server['REQUEST_URI'] = 'some-url';

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/index.html.php';
    $expectedPageTitle = 'PyAngelo Blog';
    $expectedMetaDescription = "A list of interesting coding related blog posts from the creators of PyAngelo.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
