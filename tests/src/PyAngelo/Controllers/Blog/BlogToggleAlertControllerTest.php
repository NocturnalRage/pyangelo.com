<?php
namespace Tests\src\PyAngelo\Controllers\Blog;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Blog\BlogToggleAlertController;

class BlogToggleAlertControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->blogRepository = Mockery::mock('PyAngelo\Repositories\BlogRepository');
    $this->controller = new BlogToggleAlertController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Blog\BlogToggleAlertController');
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('info', $responseVars['status']);
    $this->assertSame('Log in to update your notifications', $responseVars['message']);
  }

  public function testWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('Please update your notifications from the PyAngelo website.', $responseVars['message']);
  }

  public function testWhenNoBlogId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must select a blog to be notified about.', $responseVars['message']);
  }

  public function testWhenNotRealBlog() {
    $this->request->post['blogId'] = 100;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->blogRepository
      ->shouldReceive('getBlogById')
      ->once()
      ->with($this->request->post['blogId'])
      ->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must select a valid blog to be notified about.', $responseVars['message']);
  }

  public function testToggleAlertWhenAlreadyAlerted() {
    $blogId = 100;
    $personId = 2;
    $blog = [ 'blog_id' => $blogId ];
    $this->request->post['blogId'] = $blogId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->blogRepository
      ->shouldReceive('getBlogById')
      ->once()
      ->with($this->request->post['blogId'])
      ->andReturn($blog);
    $this->blogRepository
      ->shouldReceive('shouldUserReceiveAlert')
      ->once()
      ->with($this->request->post['blogId'], $personId)
      ->andReturn($blog);
    $this->blogRepository
      ->shouldReceive('removeFromBlogAlert')
      ->once()
      ->with($this->request->post['blogId'], $personId);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('info', $responseVars['status']);
    $this->assertSame('Notifications are off for this blog', $responseVars['message']);
  }

  public function testToggleAlertWhenNotAlerted() {
    $blogId = 100;
    $personId = 2;
    $blog = [ 'blog_id' => $blogId ];
    $this->request->post['blogId'] = $blogId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->blogRepository
      ->shouldReceive('getBlogById')
      ->once()
      ->with($this->request->post['blogId'])
      ->andReturn($blog);
    $this->blogRepository
      ->shouldReceive('shouldUserReceiveAlert')
      ->once()
      ->with($this->request->post['blogId'], $personId)
      ->andReturn(NULL);
    $this->blogRepository
      ->shouldReceive('addToBlogAlert')
      ->once()
      ->with($this->request->post['blogId'], $personId);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('success', $responseVars['status']);
    $this->assertSame('Notifications are on for this blog', $responseVars['message']);
  }
}
?>
