<?php
namespace Tests\PyAngelo\Repositories;

use PHPUnit\Framework\TestCase;
use PyAngelo\Repositories\MysqlBlogRepository;
use Tests\Factory\TestData;

class MysqlBlogRepositoryTest extends TestCase {
  protected $dbh;
  protected $blogRepository;
  protected $testData;
  protected $personId;
  protected $blogCategoryId;

  public function setUp(): void {
    $dotenv  = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../../../', '.env.test');
    $dotenv->load();
    $this->dbh = new \Mysqli(
      $_ENV['DB_HOST'],
      $_ENV['DB_USERNAME'],
      $_ENV['DB_PASSWORD'],
      $_ENV['DB_DATABASE']
    );
    $this->dbh->begin_transaction();
    $this->blogRepository = new MysqlBlogRepository($this->dbh);
    $this->testData = new TestData($this->dbh);
    $this->personId = 1;
    $this->blogCategoryId = 1;
    $this->testData->createCountry('US', 'United States', 'USD');
    $this->testData->createPerson($this->personId, 'admin@pyangelo.com');
    $this->testData->createBlogCategory($this->blogCategoryId);
  }

  public function tearDown(): void {
    $this->dbh->rollback();
    $this->dbh->close();
  }

  public function testGetBlogBySlugAndId() {
    $title = 'My First Blog Post';
    $slug = 'my-first-blog-post';
    $this->testData->createBlog(
      $title, $slug, $this->blogCategoryId, $this->personId
    );
    $blog = $this->blogRepository->getBlogBySlug($slug);
    $this->assertEquals($title, $blog['title']);
    $this->assertEquals($slug, $blog['slug']);

    $blogId = $blog['blog_id'];
    $blog2 = $this->blogRepository->getBlogById($blogId);
    $this->assertEquals($title, $blog2['title']);
    $this->assertEquals($slug, $blog2['slug']);
  }

  public function testGetAllBlogs() {
    $title = 'My First Blog Post';
    $slug = 'my-first-blog-post';
    $title2 = 'My Second Blog Post';
    $slug2 = 'my-second-blog-post';
    $this->testData->createBlog(
      $title, $slug, $this->blogCategoryId, $this->personId
    );
    $this->testData->createBlog(
      $title2, $slug2, $this->blogCategoryId, $this->personId
    );
    $blogs = $this->blogRepository->getAllBlogs();
    $this->assertEquals(2, count($blogs));
    $this->assertEquals($title, $blogs[0]['title']);
    $this->assertEquals($slug, $blogs[0]['slug']);
    $this->assertEquals($title2, $blogs[1]['title']);
    $this->assertEquals($slug2, $blogs[1]['slug']);
  }

  public function testGetFeaturedBlogs() {
    $title = 'My First Blog Post';
    $slug = 'my-first-blog-post';
    $featured = 1;
    $this->testData->createBlog(
      $title, $slug, $this->blogCategoryId, $this->personId, $featured
    );
    $blogs = $this->blogRepository->getFeaturedBlogs();
    $this->assertEquals(1, count($blogs));
    $this->assertEquals($title, $blogs[0]['title']);
    $this->assertEquals($slug, $blogs[0]['slug']);
    $this->assertEquals($featured, $blogs[0]['featured']);
  }

  public function testGetLatestBlogPost() {
    $title = 'My First Blog Post';
    $slug = 'my-first-blog-post';
    $title2 = 'My Second Blog Post';
    $slug2 = 'my-second-blog-post';
    $featured = 0;
    $this->testData->createBlog(
      $title, $slug, $this->blogCategoryId, $this->personId, $featured
    );
    $this->testData->createBlog(
      $title2, $slug2, $this->blogCategoryId, $this->personId, $featured
    );
    $blog = $this->blogRepository->getLatestBlogPost();
    $this->assertEquals($title, $blog['title']);
    $this->assertEquals($slug, $blog['slug']);
    $this->assertEquals($featured, $blog['featured']);
  }

  public function testInsertPublishedBlogAndUpdateBlogWithFormData() {
    $formData = [
      'person_id' => $this->personId,
      'title' => 'A Test Blog',
      'preview' => 'Blog intro.',
      'content' => 'A very good blog',
      'slug' => 'a-test-blog',
      'blog_image' => 'blog-image.jpg',
      'blog_category_id' => 1,
      'featured' => 0
    ];
    $blogId = $this->blogRepository->insertPublishedBlog($formData);
    $blog = $this->blogRepository->getBlogBySlug($formData['slug']);
    $this->assertEquals($formData['person_id'], $blog['person_id']);
    $this->assertEquals($formData['title'], $blog['title']);
    $this->assertEquals($formData['content'], $blog['content']);
    $this->assertEquals($formData['slug'], $blog['slug']);
    $this->assertEquals($formData['blog_category_id'], $blog['blog_category_id']);
    $formData = [
      'title' => 'A Test Blog Updated',
      'preview' => 'Blog intro update.',
      'content' => 'A very good blog updated',
      'slug' => 'a-test-blog',
      'blog_category_id' => 1,
      'featured' => 1
    ];
    $rowsUpdated = $this->blogRepository->updateBlogWithFormData($formData);
    $this->assertEquals($rowsUpdated, 1);
    $blog = $this->blogRepository->getBlogBySlug($formData['slug']);
    $this->assertEquals($formData['title'], $blog['title']);
    $this->assertEquals($formData['content'], $blog['content']);
    $this->assertEquals($formData['blog_category_id'], $blog['blog_category_id']);
    $this->assertEquals($formData['featured'], $blog['featured']);
  }

  public function testGetAllBlogCategories() {
    // First added by setup. Let's add a second category
    $blogCategoryId = 2;
    $this->testData->createBlogCategory($blogCategoryId);
    $categories = $this->blogRepository->getAllBlogCategories();
    $this->assertEquals(2, count($categories));
    $this->assertEquals(1, $categories[0]['blog_category_id']);
    $this->assertEquals($blogCategoryId, $categories[1]['blog_category_id']);
  }

  public function testGetBlogCategoryById() {
    $blogCategoryId = 1;
    $blogCategory = $this->blogRepository->getBlogCategoryById($blogCategoryId);
    $this->assertEquals($blogCategoryId, $blogCategory['blog_category_id']);
    $this->assertEquals('Coding Advice', $blogCategory['description']);
  }

  public function testGetLatestImages() {
    $this->testData->createBlogImage(1);
    $this->testData->createBlogImage(2);
    $blogImages = $this->blogRepository->getLatestImages();
    $this->assertEquals(2, count($blogImages));
    $this->assertEquals(1, $blogImages[0]['image_id']);
    $this->assertEquals(2, $blogImages[1]['image_id']);
  }

  public function testSaveBlogImage() {
    $imageName = 'test.jpg';
    $imageWidth = 640;
    $imageHeight = 360;
    $blogImageId = $this->blogRepository->saveBlogImage('test.jpg', 640, 360);
    $blogImages = $this->blogRepository->getLatestImages();
    $this->assertEquals($imageName, $blogImages[0]['image_name']);
    $this->assertEquals($imageWidth, $blogImages[0]['image_width']);
    $this->assertEquals($imageHeight, $blogImages[0]['image_height']);
  }

  public function testUpdateBlogImageBySlug() {
    $title = 'My First Blog Post';
    $slug = 'my-first-blog-post';
    $newImageName = 'updated.jpg';
    $this->testData->createBlog(
      $title, $slug, $this->blogCategoryId, $this->personId
    );
    $rowsUpdated = $this->blogRepository->updateBlogImageBySlug(
      $slug, $newImageName
    );
    $this->assertEquals(1, $rowsUpdated);
    $blog = $this->blogRepository->getBlogBySlug($slug);
    $this->assertEquals($newImageName, $blog['blog_image']);
  }

  public function testInsertBlogCommentGetPublishedBlogCommentsLatest() {
    $title = 'My First Blog Post';
    $slug = 'my-first-blog-post';
    $this->testData->createBlog(
      $title, $slug, $this->blogCategoryId, $this->personId
    );
    $blog = $this->blogRepository->getBlogBySlug($slug);
    $comment = 'This is a comment';
    $commentData = [
      'blog_id' => $blog['blog_id'],
      'person_id' => $this->personId,
      'blog_comment' => $comment,
      'published' => 1,
    ];
    $commentId = $this->blogRepository->insertBlogComment($commentData);
    $comments = $this->blogRepository->getPublishedBlogComments($blog['blog_id']);
    $this->assertEquals(1, count($comments));
    $this->assertEquals($commentId, $comments[0]['comment_id']);
    $this->assertEquals($comment, $comments[0]['blog_comment']);

    $latest = $this->blogRepository->getLatestComments(0, 10);
    $this->assertEquals($commentId, $latest[0]['comment_id']);
    $this->assertEquals($comment, $latest[0]['blog_comment']);

    $commentId = $this->blogRepository->insertBlogComment($commentData);
    $comments = $this->blogRepository->getPublishedBlogComments($blog['blog_id']);
    $this->assertEquals(2, count($comments));
    $rowsUpdated = $this->blogRepository->unpublishCommentById($commentId);
    $this->assertEquals(1, $rowsUpdated);
    $comments = $this->blogRepository->getPublishedBlogComments($blog['blog_id']);
    $this->assertEquals(1, count($comments));
  }

  public function testBlogAlerts() {
    $title = 'My First Blog Post';
    $slug = 'my-first-blog-post';
    $this->testData->createBlog(
      $title, $slug, $this->blogCategoryId, $this->personId
    );
    $blog = $this->blogRepository->getBlogBySlug($slug);
    $rowsUpdated = $this->blogRepository->addToBlogAlert(
      $blog['blog_id'], $this->personId
    );
    $this->assertEquals(1, $rowsUpdated);

    $blogId = $this->blogRepository->shouldUserReceiveAlert(
      $blog['blog_id'], $this->personId
    );
    $this->assertEquals($blog['blog_id'], $blogId['blog_id']);

    $followers = $this->blogRepository->getFollowers($blog['blog_id']);
    $this->assertEquals($followers, [['person_id' => $this->personId]]);

    $rowsDeleted = $this->blogRepository->removeFromBlogAlert(
      $blog['blog_id'], $this->personId
    );
    $this->assertEquals(1, $rowsDeleted);

    $followers = $this->blogRepository->getFollowers($blog['blog_id']);
    $this->assertEquals($followers, []);
  }
}

