<?php
namespace Tests\src\PyAngelo\Controllers\Lessons;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Lessons\LessonsGetNextVideoController;

class LessonsGetNextVideoControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->controller = new LessonsGetNextVideoController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Lessons\LessonsGetNextVideoController');
  }

  public function testInvalidVariables() {
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/next-video.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('Invalid next video request.', $responseVars['message']);
    $this->assertSame('none', $responseVars['lessonTitle']);
    $this->assertSame('none', $responseVars['tutorialSlug']);
    $this->assertSame('none', $responseVars['lessonSlug']);
  }

  public function testGetNextLesson() {
    $tutorialId = 1;
    $displayOrder = 1;
    $lessonTitle = 'Test Title';
    $tutorialSlug = 'a-tutorial';
    $lessonSlug = 'a-lesson';
    $lesson = [
      'lesson_id' => 1,
      'lesson_title' => $lessonTitle,
      'tutorial_slug' => $tutorialSlug,
      'lesson_slug' => $lessonSlug
    ];
    $this->request->get['tutorialId'] = $tutorialId;
    $this->request->get['displayOrder'] = $displayOrder;
    $this->tutorialRepository
      ->shouldReceive('getNextLessonInTutorial')
      ->once()
      ->with($tutorialId, $displayOrder)
      ->andReturn($lesson);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/next-video.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('success', $responseVars['status']);
    $this->assertSame('Next video retrieved.', $responseVars['message']);
    $this->assertSame($lessonTitle, $responseVars['lessonTitle']);
    $this->assertSame($tutorialSlug, $responseVars['tutorialSlug']);
    $this->assertSame($lessonSlug, $responseVars['lessonSlug']);
  }

  public function testGetCompletedTutorialLesson() {
    $tutorialId = 1;
    $displayOrder = 10;
    $this->request->get['tutorialId'] = $tutorialId;
    $this->request->get['displayOrder'] = $displayOrder;
    $this->tutorialRepository
      ->shouldReceive('getNextLessonInTutorial')
      ->once()
      ->with($tutorialId, $displayOrder)
      ->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/next-video.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('completed', $responseVars['status']);
    $this->assertSame('Last video in tutorial.', $responseVars['message']);
    $this->assertSame('none', $responseVars['lessonTitle']);
    $this->assertSame('none', $responseVars['tutorialSlug']);
    $this->assertSame('none', $responseVars['lessonSlug']);
  }
}
?>
