<?php
namespace tests\src\PyAngelo\Controllers\Categories;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Categories\CategoriesSortController;

class CategoriesSortControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->controller = new CategoriesSortController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Categories\CategoriesSortController');
  }

  public function testWhenNotAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testWhenAdmin() {
    $categorySlug = 'introduction-to-pyangelo';
    $tutorials = [
      [
        'tutorial_category_id' => 1,
        'category' => 'Introduction to PyAngelo',
        'category_slug' => 'introduction-to-pyangelo',
        'tutorial_title' => 'A great tutorial',
        'tutorial_slug' => 'a-great-tutorial'
      ]
    ];
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository->shouldReceive('getTutorialsByCategory')
      ->once()
      ->with($categorySlug)
      ->andReturn($tutorials);
    $this->request->get['slug'] = $categorySlug;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'categories/sort.html.php';
    $expectedPageTitle = 'Sort PyAngelo Tutorials';
    $expectedMetaDescription = 'A page where you can change the order PyAngelo tutorials are displayed in.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
