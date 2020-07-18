<?php
namespace PyAngelo\Controllers\Blog;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\BlogRepository;
use Framework\{Request, Response};
use Framework\Contracts\PurifyContract;
use Framework\Contracts\AvatarContract;

class BlogCommentController extends Controller {
  protected $blogRepository;
  protected $purifier;
  protected $avatar;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    BlogRepository $blogRepository,
    PurifyContract $purifier,
    AvatarContract $avatar
  ) {
    parent::__construct($request, $response, $auth);
    $this->blogRepository = $blogRepository;
    $this->purifier = $purifier;
    $this->avatar = $avatar;
  }

  public function exec() {
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
}
