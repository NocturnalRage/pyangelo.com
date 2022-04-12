<?php
namespace Tests\PyAngelo\Repositories;

use PHPUnit\Framework\TestCase;
use PyAngelo\Repositories\MysqlBlogRepository;
use Tests\Factory\TestData;

class MysqlBlogRepositoryTest extends TestCase {
  protected $dbh;
  protected $stripeRepository;
  protected $testData;

  public function setUp(): void {
    $dotenv  = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../../../', '.env.test');
    $dotenv->load();
    $this->dbh = new \Mysqli(
      $_ENV['DB_HOST'],
      $_ENV['DB_USERNAME'],
      $_ENV['DB_PASSWORD'],
      $_ENV['DB_DATABASE']
    );
    $this->blogRepository = new MysqlBlogRepository($this->dbh);
    $this->testData = new TestData($this->dbh);
  }

  public function tearDown(): void {
    $this->dbh->close();
  }

  public function testGetBlogBySlugAndId() {
    $title = 'My First Blog Post';
    $slug = 'my-first-blog-post';
    $this->testData->createBlog($title, $slug);
    $blog = $this->blogRepository->getBlogBySlug($slug);
    $this->assertEquals($title, $blog['title']);
    $this->assertEquals($slug, $blog['slug']);

    $blogId = $blog['blog_id'];
    $blog = $this->blogRepository->getBlogById($blogId);
    $this->assertEquals($title, $blog['title']);
    $this->assertEquals($slug, $blog['slug']);
  }

  public function testGetAllBlogs() {
    $title = 'My First Blog Post';
    $slug = 'my-first-blog-post';
    $this->testData->createBlog($title, $slug);
    $blogs = $this->blogRepository->getAllBlogs();
    $this->assertEquals($title, $blogs[0]['title']);
    $this->assertEquals($slug, $blogs[0]['slug']);
  }

  public function testGetFeaturedBlogs() {
    $title = 'My First Blog Post';
    $slug = 'my-first-blog-post';
    $featured = 1;
    $this->testData->createBlog($title, $slug, $featured);
    $blogs = $this->blogRepository->getFeaturedBlogs();
    $this->assertEquals($title, $blogs[0]['title']);
    $this->assertEquals($slug, $blogs[0]['slug']);
    $this->assertEquals($featured, $blogs[0]['featured']);
  }

  public function testGetLatestBlogPost() {
    $title = 'My First Blog Post';
    $slug = 'my-first-blog-post';
    $featured = 1;
    $this->testData->createBlog($title, $slug, $featured);
    $blog = $this->blogRepository->getLatestBlogPost();
    $this->assertEquals($title, $blog['title']);
    $this->assertEquals($slug, $blog['slug']);
    $this->assertEquals($featured, $blog['featured']);
  }

  public function testInsertPublishedBlogAndUpdateBlogWithFormData() {
    $this->testData->deleteAllBlogs();
    $this->testData->deleteAllBlogCategories();
    $this->testData->deleteAllPeople();
    $personId = 1;
    $blogCategoryId = 1;
    $this->testData->createPerson($personId, 'coder@hotmail.com');
    $this->testData->createBlogCategory($blogCategoryId);
    $formData = [
      'person_id' => $personId,
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
    $blogCategoryId = 1;
    $this->testData->deleteAllBlogs();
    $this->testData->deleteAllBlogCategories();
    $this->testData->createBlogCategory($blogCategoryId);
    $categories = $this->blogRepository->getAllBlogCategories();
    $this->assertEquals($blogCategoryId, $categories[0]['blog_category_id']);
  }

  public function testGetBlogCategoryById() {
    $this->testData->deleteAllBlogCategories();
    $blogCategoryId = 1;
    $this->testData->createBlogCategory($blogCategoryId);
    $blogCategory = $this->blogRepository->getBlogCategoryById($blogCategoryId);
    $this->assertEquals($blogCategoryId, $blogCategory['blog_category_id']);
    $this->assertEquals('Coding Advice', $blogCategory['description']);
  }

  public function testGetLatestImages() {
    $this->testData->deleteAllBlogImages();
    $blogImageId = 1;
    $this->testData->createBlogImage($blogImageId);
    $blogImages = $this->blogRepository->getLatestImages();
    $this->assertEquals($blogImageId, $blogImages[0]['image_id']);
  }

  public function testSaveBlogImage() {
    $imageName = 'test.jpg';
    $imageWidth = 640;
    $imageHeight = 360;
    $this->testData->deleteAllBlogImages();
    $blogImageId = $this->blogRepository->saveBlogImage('test.jpg', 640, 360);
    $blogImages = $this->blogRepository->getLatestImages();
    $this->assertEquals($imageName, $blogImages[0]['image_name']);
    $this->assertEquals($imageWidth, $blogImages[0]['image_width']);
    $this->assertEquals($imageHeight, $blogImages[0]['image_height']);
  }
}

