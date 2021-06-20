<?php
namespace Tests\src\PyAngelo\Controllers\Tutorials;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Tutorials\TutorialsNewController;

class TutorialsNewControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->sketchRepository = Mockery::mock('PyAngelo\Repositories\SketchRepository');
    $this->controller = new TutorialsNewController (
      $this->request,
      $this->response,
      $this->auth,
      $this->tutorialRepository,
      $this->sketchRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Tutorials\TutorialsNewController');
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
    $sketchOwnerId = 1;
    $categories = [
      [
        'tutorial_category_id' => 1,
        'category' => '3x3 Videos',
        'category_slug' => '3x3'
      ],
      [
        'tutorial_category_id' => 2,
        'category' => '3x3 Algorithms',
        'category_slug' => '3x3-algs'
      ],
    ];
    $levels = [
      [
        'tutorial_level_id' => 1,
        'description' => 'Beginner'
      ],
      [
        'tutorial_level_id' => 2,
        'description' => 'Advanced'
      ],
    ];
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository->shouldReceive('getAllTutorialCategories')
      ->once()
      ->with()
      ->andReturn($categories);
    $this->tutorialRepository->shouldReceive('getAllTutorialLevels')
      ->once()
      ->with()
      ->andReturn($levels);
    $this->sketchRepository->shouldReceive('getSketches')
      ->once()
      ->with($sketchOwnerId);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'tutorials/new.html.php';
    $expectedPageTitle = 'Create a New Tutorial';
    $expectedMetaDescription = 'Create a tutorial for PyAngelo which will consist of a number of video lessons.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
