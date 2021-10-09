<?php
namespace Tests\src\PyAngelo\Controllers\Quizzes;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Quizzes\QuizzesSkillShowController;

class QuizzesSkillShowControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->quizRepository = Mockery::mock('PyAngelo\Repositories\QuizRepository');
    $this->controller = new QuizzesSkillShowController (
      $this->request,
      $this->response,
      $this->auth,
      $this->tutorialRepository,
      $this->quizRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Quizzes\QuizzesSkillShowController');
  }

  public function testQuizzesSkillShowWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testQuizzesSkillShowWhenNoTutorialSlug() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testQuizzesSkillShowWhenNoSkillSlug() {
    $slug = 'a-tutorial';
    $this->request->get['slug'] = $slug;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testQuizzesSkillShowWhenNotValidTutorial() {
    $slug = 'a-tutorial';
    $this->request->get['slug'] = $slug;
    $skillSlug = 'a-skill';
    $this->request->get['skill_slug'] = $skillSlug;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialbySlug')->once()->with($slug)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testQuizzesSkillShowWhenNotValidSkill() {
    $slug = 'a-tutorial';
    $this->request->get['slug'] = $slug;
    $skillSlug = 'a-skill';
    $this->request->get['skill_slug'] = $skillSlug;
    $tutorialId = 10;
    $tutorialTitle = 'title';
    $tutorial = [
      'tutorial_id' => $tutorialId,
      'slug' => $slug,
      'title' => $tutorialTitle
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialbySlug')->once()->with($slug)->andReturn($tutorial);
    $this->quizRepository->shouldReceive('getSkillbySlug')->once()->with($skillSlug)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testQuizzesSkillShowWhenQuizExists() {
    $personId = 101;
    $slug = 'a-tutorial';
    $this->request->get['slug'] = $slug;
    $skillSlug = 'a-skill';
    $this->request->get['skill_slug'] = $skillSlug;
    $tutorialId = 10;
    $tutorialTitle = 'title';
    $tutorial = [
      'tutorial_id' => $tutorialId,
      'slug' => $slug,
      'title' => $tutorialTitle
    ];
    $skillId = 5;
    $skillName = "Colours";
    $skill = [
      'skill_id' => $skillId,
      'slug' => $skillSlug,
      'skill_name' => $skillName
    ];
    $quizId = 10;
    $skillQuizInfo = [
      'quiz_id' => $quizId
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository->shouldReceive('getTutorialbySlug')->once()->with($slug)->andReturn($tutorial);
    $this->quizRepository->shouldReceive('getSkillbySlug')->once()->with($skillSlug)->andReturn($skill);
    $this->quizRepository->shouldReceive('getIncompleteSkillQuizInfo')->once()->with($skillId, $personId)->andReturn($skillQuizInfo);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/show.html.php';
    $expectedPageTitle = $skillName . ' Quiz';
    $expectedMetaDescription = 'Take a skill quiz to show what you have learnt on the PyAngelo website';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  public function testQuizzesSkillShowWhenNoQuiz() {
    $personId = 101;
    $slug = 'a-tutorial';
    $this->request->get['slug'] = $slug;
    $skillSlug = 'a-skill';
    $this->request->get['skill_slug'] = $skillSlug;
    $tutorialId = 10;
    $tutorialTitle = 'title';
    $tutorial = [
      'tutorial_id' => $tutorialId,
      'slug' => $slug,
      'title' => $tutorialTitle
    ];
    $skillId = 5;
    $skillName = "Colours";
    $skill = [
      'skill_id' => $skillId,
      'slug' => $skillSlug,
      'skill_name' => $skillName
    ];
    $skills = [
      [
        'skill_id' => 1
      ]
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository->shouldReceive('getTutorialbySlug')->once()->with($slug)->andReturn($tutorial);
    $this->quizRepository->shouldReceive('getSkillbySlug')->once()->with($skillSlug)->andReturn($skill);
    $this->quizRepository->shouldReceive('getIncompleteSkillQuizInfo')->once()->with($skillId, $personId)->andReturn();
    $this->quizRepository->shouldReceive('getSkillMastery')->once()->with($skillId, $personId)->andReturn($skills);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/show-quiz-not-created.html.php';
    $expectedPageTitle = $skillName . ' Quiz';
    $expectedMetaDescription = 'Take a skill quiz to show what you have learnt on the PyAngelo website';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
