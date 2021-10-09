<?php
namespace Tests\src\PyAngelo\Controllers\Quizzes;

use PHPUnit\Framework\TestCase;
use Mockery;
use Dotenv\Dotenv;
use Framework\Request;
use Framework\Response;
use PyAngelo\Repositories\TutorialRepository;
use PyAngelo\Controllers\Quizzes\QuizzesFetchQuestionsController;

class QuizzesFetchQuestionsControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->quizRepository = Mockery::mock('PyAngelo\Repositories\QuizRepository');
    $this->controller = new QuizzesFetchQuestionsController (
      $this->request,
      $this->response,
      $this->auth,
      $this->quizRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Quizzes\QuizzesFetchQuestionsController');
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/options.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must be logged in to fetch your quiz options."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoTutorialId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/options.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must select a quiz to fetch options for."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoInvalidTutorial() {
    $quizId = 99;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->quizRepository->shouldReceive('getQuizOptions')->once()->with($quizId)->andReturn();
    $this->request->get['quizId'] = $quizId;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/options.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must select a valid quiz to fetch options for."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenQuizNotForPerson() {
    $quizId = 99;
    $personId = 199;
    $quizPersonId = 201;
    $quizOptions = [
      [
        "person_id" => $quizPersonId
      ]
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->quizRepository->shouldReceive('getQuizOptions')->once()->with($quizId)->andReturn($quizOptions);
    $this->request->get['quizId'] = $quizId;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/options.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must select your own quiz."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testSuccessGetQuestions() {
    $quizId = 99;
    $personId = 199;
    $quizPersonId = 199;
    $currentSkillQuestionId = 2;
    $quizOptions = [
      [
        "person_id" => $quizPersonId,
        "slug" => 'a-tutorial',
        "question" => 'What is it?',
        "skill_question_id" => $currentSkillQuestionId,
        "skill_question_type_id" => 1,
        "skill_question_option_id" => 1,
        "option_text" => "What is it?",
        "option_order" => 1,
        "correct" => 0,
      ]
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->quizRepository->shouldReceive('getQuizOptions')->once()->with($quizId)->andReturn($quizOptions);
    $this->quizRepository->shouldReceive('getSkillQuestionHints')->once()->with($currentSkillQuestionId)->andReturn($quizOptions);
    $this->request->get['quizId'] = $quizId;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/options.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Questions retrieved"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }
}
?>
