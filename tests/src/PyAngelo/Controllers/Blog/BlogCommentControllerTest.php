<?php
namespace Tests\src\PyAngelo\Controllers\Blog;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Blog\BlogCommentController;

class BlogCommentControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->blogRepository = Mockery::mock('PyAngelo\Repositories\BlogRepository');
    $this->purifier = Mockery::mock('Framework\Contracts\PurifyContract');
    $this->avatar = Mockery::mock('Framework\Contracts\AvatarContract');
    $this->controller = new BlogCommentController (
      $this->request,
      $this->response,
      $this->auth,
      $this->blogRepository,
      $this->purifier,
      $this->avatar
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Blog\BlogCommentController');
  }

  public function testBlogControllerWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/blog-comment.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must be logged in to add a comment."';
    $expectedCommentHtml = '"We could not add your comment due to errors."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $this->assertSame($expectedCommentHtml, $responseVars['commentHtml']);
  }

  public function testBlogCommentInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/blog-comment.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"Please add a comment from the PyAngelo website."';
    $expectedCommentHtml = '"We could not add your comment due to errors."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $this->assertSame($expectedCommentHtml, $responseVars['commentHtml']);
  }

  public function testNoBlogId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/blog-comment.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"Please add a comment to a blog."';
    $expectedCommentHtml = '"We could not add your comment due to errors."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $this->assertSame($expectedCommentHtml, $responseVars['commentHtml']);
  }

  public function testBlogCommentNoComment() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->request->post['blogId'] = 1;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/blog-comment.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"A comment must contain some text."';
    $expectedCommentHtml = '"We could not add your comment due to errors."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $this->assertSame($expectedCommentHtml, $responseVars['commentHtml']);
  }

  public function testInvalidBlog() {
    $personId = 99;
    $blogId = 1;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->blogRepository->shouldReceive('getBlogById')->once()->with($blogId)->andReturn(NULL);
    $this->request->post['blogId'] = $blogId;
    $this->request->post['blogComment'] = 'Great comment';

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/blog-comment.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must add a comment to a valid blog."';
    $expectedCommentHtml = '"We could not add your comment due to errors."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $this->assertSame($expectedCommentHtml, $responseVars['commentHtml']);
  }

  public function testBlogCommentValidComment() {
    $personId = 99;
    $email = 'fastfred@hotmail.com';
    $person = [
      'person_id' => $personId,
      'given_name' => 'Fast',
      'family_name' => 'Fred',
      'email' => $email
    ];
    $blogId = 1;
    $blog = [
      'blog_id' => $blogId,
      'slug' => 'great-blog',
      'title' => 'A Great Blog'
    ];
    $blogComment = 'Great blog';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('person')->times(4)->with()->andReturn($person);
    $this->blogRepository->shouldReceive('getBlogById')->once()->with($blogId)->andReturn($blog);
    $this->blogRepository->shouldReceive('insertBlogComment')->once();
    $this->blogRepository->shouldReceive('getFollowers')->once()->andReturn([]);
    $this->purifier->shouldReceive('purify')->once()->with($blogComment)->andReturn($blogComment);
    $this->avatar->shouldReceive('getAvatarUrl')->twice()->with($email)->andReturn('avatar');
    $this->avatar->shouldReceive('setSizeInPixels')->once()->with(25);
    $this->request->post['blogId'] = $blogId;
    $this->request->post['blogComment'] = $blogComment;
    $this->request->server['REQUEST_SCHEME'] = 'https';
    $this->request->server['SERVER_NAME'] = 'www.pyangelo.com';

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/blog-comment.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Your comment has been added."';
    $expectedCommentHtml = '"    <div class=\"media\">\n      <div class=\"media-left\">\n        <img class=\"media-object\" src=\"avatar\" alt=\"Fast Fred\" \/>\n      <\/div>\n      <div class=\"media-body\">\n        <h4 class=\"media-heading\">Fast Fred <small><i>Posted now<\/i><\/small><\/h4>\n        <p>Great blog<\/p>\n      <\/div>\n      <hr \/>\n    <\/div>"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $this->assertSame($expectedCommentHtml, $responseVars['commentHtml']);
  }
}
?>
