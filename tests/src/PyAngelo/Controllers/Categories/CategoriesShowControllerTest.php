<?php
namespace Tests\src\PyAngelo\Controllers\Categories;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Categories\CategoriesShowController;

class CategoriesShowControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->controller = new CategoriesShowController (
      $this->request,
      $this->response,
      $this->auth,
      $this->tutorialRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Categories\CategoriesShowController');
  }

  public function testWithNoSlug() {
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testWithInvalidSlug() {
    $categorySlug = 'no-such-tutorial';
    $this->request->get['slug'] = $categorySlug;
    $this->tutorialRepository->shouldReceive('getTutorialsByCategory')
      ->once()
      ->with($categorySlug)
      ->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testWithValidSlug() {
    $category = 'Introduction to PyAngelo';
    $categorySlug = 'introduction-to-pyangelo';
    $tutorials = [
      [
        'tutorial_id' => 1,
        'tutorial_category_id' => 1,
        'category' => $category,
        'category_slug' => $categorySlug,
        'title' => 'A Great Tutorial',
        'slug' => 'a-great-tutorial',
        'description' => 'What a great tutorial',
        'level' => 1,
        'percent_complete' => 50,
        'lesson_count' => 10
      ]
    ];
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository->shouldReceive('getTutorialsByCategory')
      ->once()
      ->with($categorySlug)
      ->andReturn($tutorials);
    $this->request->get['slug'] = $categorySlug;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'categories/show.html.php';
    $expectedPageTitle = 'PyAngelo Tutorials | ' . $category;
    $expectedMetaDescription = "Learn to code using Python graphics programming in the browser.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
