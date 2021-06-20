<?php
namespace Tests\src\PyAngelo\Controllers\Blog;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Blog\BlogCreateController;

class BlogCreateControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->blogFormService = Mockery::mock('PyAngelo\FormServices\BlogFormService');
    $this->controller = new BlogCreateController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Blog\BlogCreateController');
  }

  public function testBlogCreateControllerWhenNotAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testBlogCreateControllerWhenAdminWithNoFormData() {
    session_start();
    $flashMessage = 'errors';
    $errors = [
      'error' => 'error'
    ];
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->blogFormService->shouldReceive('createBlog')
      ->once()
      ->with([], [])
      ->andReturn(false);
    $this->blogFormService->shouldReceive('getErrors')
      ->once()
      ->with()
      ->andReturn($errors);
    $this->blogFormService->shouldReceive('getFlashMessage')
      ->once()
      ->with()
      ->andReturn($flashMessage);
    $this->request->post = [];
    $this->request->files['blog_image'] = [];

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /blog/new'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($flashMessage, $this->request->session['flash']['message']);
    $this->assertSame($errors, $this->request->session['errors']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testBlogCreateControllerWhenAdminWithValidData() {
    session_start();
    $this->request->files['blog_image'] = [];
    $this->request->post = [];
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->blogFormService->shouldReceive('createBlog')
      ->once()
      ->with([], [])
      ->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /blog'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
