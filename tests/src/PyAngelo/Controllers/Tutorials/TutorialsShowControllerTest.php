<?php
namespace Tests\src\PyAngelo\Controllers\Tutorials;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Tutorials\TutorialsShowController;

class TutorialsShowControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->controller = new TutorialsShowController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Tutorials\TutorialsShowController');
  }

  public function testWithNoSlug() {
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testWithInvalidSlug() {
    $slug = 'no-such-tutorial';
    $this->auth->shouldReceive('personId')->once()->with()->andReturn(NULL);
    $this->tutorialRepository->shouldReceive('getTutorialBySlugWithStats')
      ->once()
      ->with($slug, 0)
      ->andReturn(NULL);
    $this->request->get['slug'] = $slug;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testWithValidSlug() {
    $slug = 'tutorial-1';
    $description = 'The first tutorial';
    $level = 'beginner';
    $percent_complete = 50;
    $lesson_count = 2;
    $title = 'Tutorial 1';
    $tutorial = [
      'tutorial_id' => 1,
      'title' => $title,
      'slug' => $slug,
      'description' => $description,
      'level' => $level,
      'percent_complete' => $percent_complete,
      'lesson_count' => $lesson_count
    ];
    $lessons = [
      [
        'lesson_id' => 1,
        'lesson_title' => 'A great lesson',
        'lesson_slug' => 'a-great-lesson',
        'display_order' => 1,
        'display_duration' => '2:43',
      ],
      [
        'lesson_id' => 2,
        'lesson_title' => 'A second great lesson',
        'lesson_slug' => 'a-second-great-lesson',
        'display_order' => 2,
        'display_duration' => '1:00',
      ]
    ];
    $skills = [
       "skill_id" => 1
    ];
    $this->auth->shouldReceive('personId')->times(3)->with()->andReturn(NULL);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository->shouldReceive('getTutorialBySlugWithStats')
      ->once()
      ->with($slug, 0)
      ->andReturn($tutorial);
    $this->tutorialRepository->shouldReceive('getTutorialLessons')
      ->once()
      ->with($tutorial['tutorial_id'], 0)
      ->andReturn($lessons);
    $this->tutorialRepository->shouldReceive('getTutorialSkills')
      ->once()
      ->with($tutorial['tutorial_id'], 0)
      ->andReturn($skills);
    $this->request->get['slug'] = $slug;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'tutorials/show.html.php';
    $expectedPageTitle = $title . ' | PyAngelo';
    $expectedMetaDescription = $description;
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
    $this->assertSame($skills, $responseVars['skills']);
  }
}
?>
