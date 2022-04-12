<?php
namespace Tests\src\PyAngelo\Controllers\Blog;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Blog\BlogUpdateController;

class BlogUpdateControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->blogFormService = Mockery::mock('PyAngelo\FormServices\BlogFormService');
    $this->controller = new BlogUpdateController (
      $this->request,
      $this->response,
      $this->auth,
      $this->blogFormService
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Blog\BlogUpdateController');
  }

  public function testBlogUpdateControllerWhenNotAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testBlogUpdateControllerWhenAdminWithNoSlug() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  /**
   * @runInSeparateProcess
   */
  public function testBlogUpdateControllerWhenAdminWithErrors() {
    session_start();
    $slug = 'no-such-slug';
    $this->request->post['slug'] = $slug;
    $this->request->files['blog_image'] = [];
    $flashMessage = 'errors';
    $errors = [
      'error' => 'error'
    ];
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->blogFormService->shouldReceive('updateBlog')
      ->once()
      ->with($this->request->post, $this->request->files['blog_image'])
      ->andReturn(false);
    $this->blogFormService->shouldReceive('getErrors')
      ->once()
      ->with()
      ->andReturn($errors);
    $this->blogFormService->shouldReceive('getFlashMessage')
      ->once()
      ->with()
      ->andReturn($flashMessage);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /blog/'. $slug . '/edit'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($flashMessage, $_SESSION['flash']['message']);
    $this->assertSame($errors, $_SESSION['errors']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testBlogUpdateControllerWhenAdminWithValidData() {
    session_start();
    $slug = 'no-such-slug';
    $this->request->post['slug'] = $slug;
    $this->request->files['blog_image'] = [];
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->blogFormService->shouldReceive('updateBlog')
      ->once()
      ->with($this->request->post, $this->request->files['blog_image'])
      ->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /blog/' . $slug));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
