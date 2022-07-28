<?php
namespace PyAngelo\Controllers\Blog;

use Carbon\Carbon;
use Framework\{Request, Response};
use Framework\Contracts\PurifyContract;
use Framework\Contracts\AvatarContract;
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\BlogRepository;
use PyAngelo\FormServices\BlogFormService;

class BlogController extends Controller {
  protected $blogRepository;
  protected $blogFormService;
  protected $purifier;
  protected $avatar;
  protected $showCommentCount;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    BlogRepository $blogRepository,
    BlogFormService $blogFormService,
    PurifyContract $purifier,
    AvatarContract $avatar,
    $showCommentCount
  ) {
    parent::__construct($request, $response, $auth);
    $this->blogRepository = $blogRepository;
    $this->blogFormService = $blogFormService;
    $this->purifier = $purifier;
    $this->avatar = $avatar;
    $this->showCommentCount = $showCommentCount;
  }

  public function index() {
    $_SESSION['redirect'] = $this->request->server['REQUEST_URI'];
    $this->response->setView('blog/index.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'PyAngelo Blog',
      'metaDescription' => "A list of interesting coding related blog posts from the creators of PyAngelo.",
      'activeLink' => 'Blog',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'blogs' => $this->blogRepository->getAllBlogs(),
      'featuredBlogs' => $this->blogRepository->getFeaturedBlogs(),
      'purifier' => $this->purifier
    ));
    return $this->response;
  }

  public function show() {
    if (!isset($this->request->get['slug']))
      return $this->redirectToPageNotFound();

    if (! $blog = $this->blogRepository->getBlogBySlug($this->request->get['slug']))
      return $this->redirectToPageNotFound();

    $alertUser = false;
    if ($this->auth->loggedIn()) {
      $alertUser = $this->blogRepository->shouldUserReceiveAlert(
        $blog['blog_id'],
        $this->auth->personId()
      ) ? true : false;
    }
    else {
      $_SESSION['redirect'] = $this->request->server['REQUEST_URI'];
    }

    $comments = $this->blogRepository->getPublishedBlogComments($blog['blog_id']);
    foreach ($comments as &$comment) {
      $comment['created_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $comment['created_at'])->diffForHumans();
    }

    $this->response->setView('blog/show.html.php');
    $this->response->setVars(array(
      'pageTitle' => $blog['title'],
      'metaDescription' => strip_tags($blog['preview']),
      'activeLink' => 'Blog',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'blog' => $blog,
      'alertUser' => $alertUser,
      'comments' => $comments,
      'purifier' => $this->purifier,
      'avatar' => $this->avatar,
      'showCommentCount' => $this->showCommentCount
    ));
    return $this->response;
  }

  public function new() {
    if (!$this->auth->isAdmin())
      return $this->redirectToHomePageWithWarning('You are not authorised!');

    $this->response->setView('blog/new.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Create a New Blog',
      'metaDescription' => "Create an amazing new blog for the PyAngelo crowd.",
      'activeLink' => 'Blog',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'categories' => $this->blogRepository->getAllBlogCategories(),
      'submitButtonText' => 'Create'
    ));
    $this->addVar('errors');
    $this->addVar('formVars');
    $this->addVar('flash');
    return $this->response;
  }

  public function create() {
    if (!$this->auth->isAdmin())
      return $this->redirectToHomePageWithWarning('You are not authorised!');

    $success = $this->blogFormService->createBlog(
      $this->request->post,
      $this->request->files['blog_image']
    );
    if (!$success) {
      $_SESSION['errors'] = $this->blogFormService->getErrors();
      $this->flash($this->blogFormService->getFlashMessage(), 'danger');
      $_SESSION['formVars'] = $this->request->post;
      $this->response->header('Location: /blog/new');
      return $this->response;
    }
    $this->response->header('Location: /blog/' . $success);
    return $this->response;
  }

  public function edit() {
    if (!$this->auth->isAdmin())
      return $this->redirectToHomePageWithWarning('You are not authorised!');

    if (!isset($this->request->get['slug']))
      return $this->redirectToPageNotFound();

    if (!($blog = $this->blogRepository->getBlogBySlug($this->request->get['slug'])))
      return $this->redirectToPageNotFound();

    $formVars = $_SESSION['formVars'] ?? $blog;
    unset($_SESSION['formVars']);

    $this->response->setView('blog/edit.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Edit ' . $blog['title'] . ' Blog',
      'metaDescription' => "Edit this PyAngelo blog.",
      'activeLink' => 'Blog',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'blog' => $blog,
      'categories' => $this->blogRepository->getAllBlogCategories(),
      'formVars' => $formVars,
      'submitButtonText' => 'Update'
    ));
    $this->addVar('errors');
    $this->addVar('flash');
    return $this->response;
  }

  public function update() {
    if (!$this->auth->isAdmin())
      return $this->redirectToHomePageWithWarning('You are not authorised!');

    if (!isset($this->request->post['slug']))
      return $this->redirectToPageNotFound();

    $success = $this->blogFormService->updateBlog(
      $this->request->post,
      $this->request->files['blog_image']
    );
    if (!$success) {
      $_SESSION['errors'] = $this->blogFormService->getErrors();
      $this->flash($this->blogFormService->getFlashMessage(), 'danger');
      $_SESSION['formVars'] = $this->request->post;
      $location = 'Location: /blog/' . urlencode($this->request->post['slug']) . '/edit';
      $this->response->header($location);
      return $this->response;
    }

    $location = 'Location: /blog/' . urlencode($this->request->post['slug']);
    $this->response->header($location);
    return $this->response;
  }

  public function toggleAlert() {
    $this->response->setView('blog/toggle-alert.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Log in to update your notifications'
      ));
      return $this->response;
    }

    // Is the CRSF Token Valid
    if (! $this->auth->crsfTokenIsValid()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'Please update your notifications from the PyAngelo website.'
      ));
      return $this->response;
    }

    if (empty($this->request->post['blogId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a blog to be notified about.'
      ));
      return $this->response;
    }

    if (! $blog = $this->blogRepository->getBlogById($this->request->post['blogId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a valid blog to be notified about.'
      ));
      return $this->response;
    }

    $alertUser = $this->blogRepository->shouldUserReceiveAlert(
      $this->request->post['blogId'],
      $this->auth->personId()
    );

    if (! $alertUser) {
      $this->blogRepository->addToBlogAlert(
        $this->request->post['blogId'],
        $this->auth->personId()
      );
      $this->response->setVars(array(
        'status' => 'success',
        'message' => 'Notifications are on for this blog'
      ));
    }
    else {
      $this->blogRepository->removeFromBlogAlert(
        $this->request->post['blogId'],
        $this->auth->personId()
      );
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Notifications are off for this blog'
      ));
    }
    return $this->response;
  }

  public function addComment() {
    $this->response->setView('blog/blog-comment.json.php');
    $this->response->header('Content-Type: application/json');

    $errorsCommentHtml = 'We could not add your comment due to errors.';

    // Are we logged in?
    if (!$this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('You must be logged in to add a comment.'),
        'commentHtml' => json_encode($errorsCommentHtml)
      ));
      return $this->response;
    }

    // Is the CRSF Token Valid
    if (! $this->auth->crsfTokenIsValid()) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('Please add a comment from the PyAngelo website.'),
        'commentHtml' => json_encode($errorsCommentHtml)
      ));
      return $this->response;
    }

    if (empty($this->request->post['blogId'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('Please add a comment to a blog.'),
        'commentHtml' => json_encode($errorsCommentHtml)
      ));
      return $this->response;
    }

    if (empty($this->request->post['blogComment'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('A comment must contain some text.'),
        'commentHtml' => json_encode($errorsCommentHtml)
      ));
      return $this->response;
    }

    // Is the blog id valid
    if (!($blog = $this->blogRepository->getBlogById($this->request->post['blogId']))) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('You must add a comment to a valid blog.'),
        'commentHtml' => json_encode($errorsCommentHtml)
      ));
      return $this->response;
    }

    $commentData = [
      'blog_id' => $blog['blog_id'],
      'person_id' => $this->auth->personId(),
      'blog_comment' => $this->request->post['blogComment'],
      'published' => 1
    ];
    $commentId = $this->blogRepository->insertBlogComment($commentData);

    // Output the comment HTML to the view
    $displayName = htmlspecialchars($this->auth->person()['given_name'] . ' ' . $this->auth->person()['family_name'], ENT_QUOTES, 'UTF-8');
    $cleanCommentHtml = $this->purifier->purify($this->request->post['blogComment']);
    $avatarUrl = $this->avatar->getAvatarUrl($this->auth->person()['email']);
    $commentHtml = <<<ENDHTML
    <div class="media">
      <div class="media-left">
        <img class="media-object" src="$avatarUrl" alt="$displayName" />
      </div>
      <div class="media-body">
        <h4 class="media-heading">$displayName <small><i>Posted now</i></small></h4>
        <p>$cleanCommentHtml</p>
      </div>
      <hr />
    </div>
ENDHTML;

    $this->notifyFollowers($blog, $commentId);

    $this->response->setVars(array(
        'status' => json_encode('success'),
        'message' => json_encode('Your comment has been added.'),
      'commentHtml' => json_encode($commentHtml)
    ));
    return $this->response;
  }

  public function unpublishComment() {
    if (!$this->auth->isAdmin())
      return $this->redirectToHomePageWithWarning('You are not authorised!');

    if (! $this->auth->crsfTokenIsValid())
      return $this->redirectToHomePageWithWarning('You must delete comments from the PyAngelo website!');

    if (!isset($this->request->post['comment_id']))
      return $this->redirectToPageNotFound();

    $this->blogRepository->unpublishCommentById($this->request->post['comment_id']);

    $location = $this->request->server['HTTP_REFERER'] ?? '/';
    $this->response->header("Location: $location");
    return $this->response;
  }

  private function notifyFollowers($blog, $commentId) {
    $person = $this->auth->person();
    $followers = $this->blogRepository->getFollowers($blog['blog_id']);
    $commentLink = '/blog/' . $blog['slug'] . '#comment_' . $commentId;
    $this->avatar->setSizeInPixels(25);
    $avatarUrl = $this->avatar->getAvatarUrl($person['email']);
    foreach ($followers as $follower) {
      if ($follower['person_id'] != $person['person_id']) {
        $data_json = json_encode([
          'message' => $person['given_name'] . ' ' . $person['family_name'] . ' left a comment on the blog "' . $blog['title'] . '"',
          'link' => $commentLink,
          'avatarUrl' => $avatarUrl,
          'isAdmin' => $this->auth->isAdmin()
        ]);
        $this->auth->createNotification(
          $follower['person_id'],
          $blog['blog_id'],
          'blog',
          $data_json
        );
      }
    }
  }

  private function redirectToPageNotFound() {
    $this->response->header('Location: /page-not-found');
    return $this->response;
  }

  private function redirectToHomePageWithWarning($warning) {
    $this->flash($warning, 'danger');
    $this->response->header('Location: /');
    return $this->response;
  }
}
