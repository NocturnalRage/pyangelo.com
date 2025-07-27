<?php
namespace Tests\src\PyAngelo\Controllers\Tutorials;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Tutorials\TutorialsEditController;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class TutorialsEditControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $tutorialRepository;
  protected $sketchRepository;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->sketchRepository = Mockery::mock('PyAngelo\Repositories\SketchRepository');
    $this->controller = new TutorialsEditController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Tutorials\TutorialsEditController');
  }

  public function testWhenNotAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testWhenAdminWithNoSlug() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  #[RunInSeparateProcess]
  public function testWhenAdminAndValidData() {
    session_start();
    $sketchOwnerId = 1;
    $slug = 'tutorial-1';
    $tutorial = [
      'title' => 'Tutorial 1',
      'slug' => $slug
    ];
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
    $this->tutorialRepository->shouldReceive('getTutorialBySlug')
      ->once()
      ->with($slug)
      ->andReturn($tutorial);
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
    $this->request->get = ['slug' => 'tutorial-1'];
    $this->request->files['thumbnail'] = [];

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'tutorials/edit.html.php';
    $expectedPageTitle = 'Edit Tutorial 1 Tutorial';
    $expectedMetaDescription = 'Edit this PyAngelo tutorial.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
