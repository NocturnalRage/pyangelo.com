<?php
namespace tests\src\PyAngelo\Controllers\Profile;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Profile\LatestCommentsController;

class LatestCommentsControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->questionRepository = Mockery::mock('PyAngelo\Repositories\QuestionRepository');
    $this->blogRepository = Mockery::mock('PyAngelo\Repositories\blogRepository');
    $this->controller = new LatestCommentsController (
      $this->request,
      $this->response,
      $this->auth,
      $this->tutorialRepository,
      $this->questionRepository,
      $this->blogRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Profile\LatestCommentsController');
  }

  /**
   * @runInSeparateProcess
   */
  public function testLatestCommentsController() {
    $personId = 99;
    $latestLessonComments = [];
    $latestQuestionComments = [];
    $latestBlogComments = [];
    session_start();
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository->shouldReceive('getLatestComments')->once()->with(0, 10)->andReturn($latestLessonComments);
    $this->questionRepository->shouldReceive('getLatestComments')->once()->with(0, 10)->andReturn($latestQuestionComments);
    $this->blogRepository->shouldReceive('getLatestComments')->once()->with(0, 10)->andReturn($latestBlogComments);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/latest-comments.html.php';
    $expectedPageTitle = 'Latest Comments | PyAngelo';
    $expectedMetaDescription = "The latest comments on the PyAngelo website.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
