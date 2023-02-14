<?php
namespace Tests\src\PyAngelo\Controllers\Quizzes;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Quizzes\QuizzesShowController;

class QuizzesShowControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $tutorialRepository;
  protected $quizRepository;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->quizRepository = Mockery::mock('PyAngelo\Repositories\QuizRepository');
    $this->controller = new QuizzesShowController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Quizzes\QuizzesShowController');
  }

  public function testQuizzesShowWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testQuizzesShowWhenNoSlug() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testQuizzesShowWhenNotValidTutorial() {
    $slug = 'a-tutorial';
    $this->request->get['slug'] = $slug;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialbySlug')->once()->with($slug)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testQuizzesShowWhenQuizExists() {
    $personId = 101;
    $slug = 'a-tutorial';
    $tutorialId = 10;
    $tutorialTitle = 'title';
    $tutorial = [
      'tutorial_id' => $tutorialId,
      'slug' => $slug,
      'title' => $tutorialTitle
    ];
    $quizId = 10;
    $tutorialQuizInfo = [
      'quiz_id' => $quizId
    ];
    $this->request->get['slug'] = $slug;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository->shouldReceive('getTutorialbySlug')->once()->with($slug)->andReturn($tutorial);
    $this->quizRepository->shouldReceive('getIncompleteTutorialQuizInfo')->once()->with($tutorialId, $personId)->andReturn($tutorialQuizInfo);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/show.html.php';
    $expectedPageTitle = $tutorialTitle . ' Quiz';
    $expectedMetaDescription = 'Take a quiz to show what you have learnt on the PyAngelo website';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  public function testQuizzesShowWhenNoQuiz() {
    $personId = 101;
    $slug = 'a-tutorial';
    $tutorialId = 10;
    $tutorialTitle = 'title';
    $tutorial = [
      'tutorial_id' => $tutorialId,
      'slug' => $slug,
      'title' => $tutorialTitle
    ];
    $skills = [
      [
        'skill_id' => 1
      ]
    ];
    $this->request->get['slug'] = $slug;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository->shouldReceive('getTutorialbySlug')->once()->with($slug)->andReturn($tutorial);
    $this->quizRepository->shouldReceive('getIncompleteTutorialQuizInfo')->once()->with($tutorialId, $personId)->andReturn();
    $this->quizRepository->shouldReceive('getTutorialSkillsMastery')->once()->with($tutorialId, $personId)->andReturn($skills);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/show-quiz-not-created.html.php';
    $expectedPageTitle = $tutorialTitle . ' Quiz';
    $expectedMetaDescription = 'Take a quiz to show what you have learnt on the PyAngelo website';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
