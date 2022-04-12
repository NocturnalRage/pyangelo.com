<?php
namespace Tests\src\PyAngelo\Controllers\Lessons;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Lessons\LessonsSortController;

class LessonsSortControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->controller = new LessonsSortController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Lessons\LessonsSortController');
  }

  public function testLessonSortWhenNotAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testLessonsSortWhenAdminAndNoSlug() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testLessonSortWhenAdmin() {
    $person = [ 'person_id' => 1 ];
    $tutorialId = 5;
    $tutorialSlug = 'a-tutorial';
    $tutorialTitle = 'A Tutorial';
    $tutorial = [
      'tutorial_id' => $tutorialId,
      'title' => $tutorialTitle,
      'slug' => $tutorialSlug
    ];
    $lessons = [
      [
        'lesson_id' => 1,
        'lesson_title' => 'A Lesson',
        'lesson_slug' => 'a-lesson'
      ]
    ];
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository
      ->shouldReceive('getTutorialBySlug')
      ->once()
      ->with($tutorialSlug)
      ->andReturn($tutorial);
    $this->tutorialRepository
      ->shouldReceive('getTutorialLessons')
      ->once()
      ->with($tutorialId, $person['person_id'])
      ->andReturn($lessons);
    $this->request->get['slug'] = $tutorialSlug;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/sort.html.php';
    $expectedPageTitle = 'Sort PyAngelo Lessons';
    $expectedMetaDescription = 'A page where you can change the order PyAngelo lessons are displayed for a tutorial.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
