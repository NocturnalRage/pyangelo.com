<?php
namespace Tests\src\PyAngelo\Controllers\Tutorials;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Tutorials\TutorialsIndexController;

class TutorialsIndexControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $tutorialRepository;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->controller = new TutorialsIndexController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Tutorials\TutorialsIndexController');
  }

  public function testWhenLoggedOut() {
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository->shouldReceive('getAllTutorials')->once()->with()->andReturn([]);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'tutorials/index.html.php';
    $expectedPageTitle = 'PyAngelo Tutorials';
    $expectedMetaDescription = "Learn how to code using Python graphics programming in the browser.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
