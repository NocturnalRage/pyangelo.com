<?php
namespace Tests\src\PyAngelo\Controllers\Skills;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Skills\SkillsIndexController;

class SkillsIndexControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->controller = new SkillsIndexController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Skills\SkillsIndexController');
  }

  public function testWhenLoggedOut() {
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository->shouldReceive('getAllSkills')->once()->with()->andReturn([]);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'skills/index.html.php';
    $expectedPageTitle = 'PyAngelo Skills Mastery';
    $expectedMetaDescription = "Your mastery level for each of the skills taught on the PyAngelo website.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
