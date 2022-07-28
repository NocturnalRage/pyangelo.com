<?php
namespace Tests\src\PyAngelo\Controllers\Blog;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Blog\BlogController;

class BlogControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->blogRepository = Mockery::mock('PyAngelo\Repositories\BlogRepository');
    $this->blogFormService = Mockery::mock('PyAngelo\FormServices\BlogFormService');
    $this->purifier = Mockery::mock('Framework\Contracts\PurifyContract');
    $this->avatar = Mockery::mock('Framework\Contracts\AvatarContract');
    $this->showCommentCount = 5;
    $this->controller = new BlogController (
      $this->request,
      $this->response,
      $this->auth,
      $this->blogRepository,
      $this->blogFormService,
      $this->purifier,
      $this->avatar,
      $this->showCommentCount
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Blog\BlogController');
  }


  /**
   * @runInSeparateProcess
   */
  public function testBlogIndex() {
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

    $response = $this->controller->index();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/index.html.php';
    $expectedPageTitle = 'PyAngelo Blog';
    $expectedMetaDescription = "A list of interesting coding related blog posts from the creators of PyAngelo.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  public function testBlogShowControllerWithNoSlug() {
    $response = $this->controller->show();
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

    $response = $this->controller->show();
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

    $response = $this->controller->show();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/show.html.php';
    $expectedPageTitle = $blog['title'];
    $expectedMetaDescription = $blog['preview'];
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  public function testBlogNewControllerWhenNotAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->new();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testBlogNewControllerWhenAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->blogRepository->shouldReceive('getAllBlogCategories')
      ->once()
      ->with()
      ->andReturn('blogCategories');

    $response = $this->controller->new();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/new.html.php';
    $expectedPageTitle = 'Create a New Blog';
    $expectedMetaDescription = "Create an amazing new blog for the PyAngelo crowd.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  public function testBlogCreateControllerWhenNotAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->create();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
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

    $response = $this->controller->create();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /blog/new'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($flashMessage, $_SESSION['flash']['message']);
    $this->assertSame($errors, $_SESSION['errors']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testBlogCreateControllerWhenAdminWithValidData() {
    session_start();
    $blogSlug = 'a-test-blog';
    $this->request->files['blog_image'] = [];
    $this->request->post = [];
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->blogFormService->shouldReceive('createBlog')
      ->once()
      ->with([], [])
      ->andReturn($blogSlug);

    $response = $this->controller->create();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /blog/' . $blogSlug));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testBlogEditControllerWhenNotAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->edit();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testBlogEditControllerWhenAdminNoSlug() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);

    $response = $this->controller->edit();
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

    $response = $this->controller->edit();
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

    $response = $this->controller->edit();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/edit.html.php';
    $expectedPageTitle = 'Edit ' . $blog['title'] . ' Blog';
    $expectedMetaDescription = "Edit this PyAngelo blog.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  public function testBlogUpdateControllerWhenNotAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->update();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testBlogUpdateControllerWhenAdminWithNoSlug() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);

    $response = $this->controller->update();
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

    $response = $this->controller->update();
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

    $response = $this->controller->update();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /blog/' . $slug));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testToggleBlogAlertWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->toggleAlert();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('info', $responseVars['status']);
    $this->assertSame('Log in to update your notifications', $responseVars['message']);
  }

  public function testToggleBlogAlertWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->toggleAlert();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('Please update your notifications from the PyAngelo website.', $responseVars['message']);
  }

  public function testToggleBlogAlertWhenNoBlogId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->toggleAlert();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must select a blog to be notified about.', $responseVars['message']);
  }

  public function testToggleBlogAlertWhenNotRealBlog() {
    $this->request->post['blogId'] = 100;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->blogRepository
      ->shouldReceive('getBlogById')
      ->once()
      ->with($this->request->post['blogId'])
      ->andReturn(NULL);

    $response = $this->controller->toggleAlert();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must select a valid blog to be notified about.', $responseVars['message']);
  }

  public function testToggleBlogAlertWhenAlreadyAlerted() {
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

    $response = $this->controller->toggleAlert();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('info', $responseVars['status']);
    $this->assertSame('Notifications are off for this blog', $responseVars['message']);
  }

  public function testToggleBlogAlertWhenNotAlerted() {
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

    $response = $this->controller->toggleAlert();
    $responseVars = $response->getVars();
    $expectedViewName = 'blog/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('success', $responseVars['status']);
    $this->assertSame('Notifications are on for this blog', $responseVars['message']);
  }

  public function testBlogAddCommentWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $response = $this->controller->addComment();
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

  public function testBlogAddCommentInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->addComment();
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

  public function testAddCommentNoBlogId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->addComment();
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

  public function testBlogAddCommentNoComment() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->request->post['blogId'] = 1;

    $response = $this->controller->addComment();
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

  public function testAddCommentInvalidBlog() {
    $personId = 99;
    $blogId = 1;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->blogRepository->shouldReceive('getBlogById')->once()->with($blogId)->andReturn(NULL);
    $this->request->post['blogId'] = $blogId;
    $this->request->post['blogComment'] = 'Great comment';

    $response = $this->controller->addComment();
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

  public function testBlogAddCommentValidComment() {
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

    $response = $this->controller->addComment();
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

  /**
   * @runInSeparateProcess
   */
  public function testUnpublishBlogCommentWhenNotAdmin() {
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->unpublishComment();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testUnpublishBlogCommentInvalidCrsfToken() {
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->andReturn(false);

    $response = $this->controller->unpublishComment();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You must delete comments from the PyAngelo website!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testUnpublishBlogCommentNoCommentId() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->unpublishComment();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testUnpublishBlogComment() {
    $commentId = 1;
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->blogRepository->shouldReceive('unpublishCommentById')
      ->once()
      ->with($commentId)
      ->andReturn(1);
    $this->request->post['comment_id'] = $commentId;

    $response = $this->controller->unpublishComment();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
