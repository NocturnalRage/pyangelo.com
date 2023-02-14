<?php
namespace Tests\src\PyAngelo\Controllers\Quizzes;

use PHPUnit\Framework\TestCase;
use Mockery;
use Dotenv\Dotenv;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Quizzes\QuizzesRecordResponseController;

class QuizzesRecordResponseControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $quizRepository;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->quizRepository = Mockery::mock('PyAngelo\Repositories\QuizRepository');
    $this->controller = new QuizzesRecordResponseController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Quizzes\QuizzesRecordResponseController');
  }

  public function testWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/record.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must record a response from the PyAngelo website."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/record.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must be logged in to record a response."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoQuizId() {
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/record.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must select a quiz to record a response for."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoSkillQuestionId() {
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $quizId = 99;
    $this->request->post['quizId'] = $quizId;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/record.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must select a quiz question to record a response for."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoSkillQuestionOptionId() {
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $quizId = 99;
    $skillQuestionId = 199;
    $this->request->post['quizId'] = $quizId;
    $this->request->post['skillQuestionId'] = $skillQuestionId;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/record.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must select a response to record it."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoCorrectUnaided() {
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $quizId = 99;
    $skillQuestionId = 199;
    $skillQuestionOptionId = 299;
    $this->request->post['quizId'] = $quizId;
    $this->request->post['skillQuestionId'] = $skillQuestionId;
    $this->request->post['skillQuestionOptionId'] = $skillQuestionOptionId;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/record.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"Did not receive the correct or incorrect flag."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoQuestionStartTime() {
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $quizId = 99;
    $skillQuestionId = 199;
    $skillQuestionOptionId = 299;
    $correctUnaided = 1;
    $this->request->post['quizId'] = $quizId;
    $this->request->post['skillQuestionId'] = $skillQuestionId;
    $this->request->post['skillQuestionOptionId'] = $skillQuestionOptionId;
    $this->request->post['correctUnaided'] = $correctUnaided;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/record.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"Did not receive the start time for the question."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoQuestionEndTime() {
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $quizId = 99;
    $skillQuestionId = 199;
    $skillQuestionOptionId = 299;
    $correctUnaided = 1;
    $questionStartTime = '2021-09-25 21:54:00';
    $this->request->post['quizId'] = $quizId;
    $this->request->post['skillQuestionId'] = $skillQuestionId;
    $this->request->post['skillQuestionOptionId'] = $skillQuestionOptionId;
    $this->request->post['correctUnaided'] = $correctUnaided;
    $this->request->post['questionStartTime'] = $correctUnaided;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/record.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"Did not receive the end time for the question."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNotValidQuiz() {
    $quizId = 99;
    $skillQuestionId = 199;
    $skillQuestionOptionId = 299;
    $correctUnaided = 1;
    $questionStartTime = '2021-09-25 21:54:00';
    $questionEndTime = '2021-09-25 21:55:00';
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->quizRepository->shouldReceive('getQuizOptions')->once()->with($quizId)->andReturn();
    $this->request->post['quizId'] = $quizId;
    $this->request->post['skillQuestionId'] = $skillQuestionId;
    $this->request->post['skillQuestionOptionId'] = $skillQuestionOptionId;
    $this->request->post['correctUnaided'] = $correctUnaided;
    $this->request->post['questionStartTime'] = $questionStartTime;
    $this->request->post['questionEndTime'] = $questionEndTime;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/record.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must select a valid quiz to record a response for."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenDifferentPeople() {
    $personId = 150;
    $quizPersonId = 151;
    $quizId = 99;
    $skillQuestionId = 199;
    $skillQuestionOptionId = 299;
    $correctUnaided = 1;
    $questionStartTime = '2021-09-25 21:54:00';
    $questionEndTime = '2021-09-25 21:55:00';
    $options = [
      [
        "person_id" => $quizPersonId
      ]
    ];
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->quizRepository->shouldReceive('getQuizOptions')->once()->with($quizId)->andReturn($options);
    $this->request->post['quizId'] = $quizId;
    $this->request->post['skillQuestionId'] = $skillQuestionId;
    $this->request->post['skillQuestionOptionId'] = $skillQuestionOptionId;
    $this->request->post['correctUnaided'] = $correctUnaided;
    $this->request->post['questionStartTime'] = $questionStartTime;
    $this->request->post['questionEndTime'] = $questionEndTime;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/record.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must select your own quiz to record a response."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenSamePerson() {
    $personId = 150;
    $quizPersonId = 150;
    $quizId = 99;
    $skillQuestionId = 199;
    $skillQuestionOptionId = 299;
    $correctUnaided = 1;
    $questionStartTime = '2021-09-25 21:54:00';
    $questionEndTime = '2021-09-25 21:55:00';
    $options = [
      [
        "person_id" => $quizPersonId
      ]
    ];
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->quizRepository->shouldReceive('getQuizOptions')->once()->with($quizId)->andReturn($options);
    $this->quizRepository
         ->shouldReceive('updateQuizQuestion')
         ->once()
         ->with(
           $quizId,
           $skillQuestionId,
           $skillQuestionOptionId,
           $correctUnaided,
           $questionStartTime,
           $questionEndTime,
         )
         ->andReturn($options);
    $this->request->post['quizId'] = $quizId;
    $this->request->post['skillQuestionId'] = $skillQuestionId;
    $this->request->post['skillQuestionOptionId'] = $skillQuestionOptionId;
    $this->request->post['correctUnaided'] = $correctUnaided;
    $this->request->post['questionStartTime'] = $questionStartTime;
    $this->request->post['questionEndTime'] = $questionEndTime;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/record.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Response recorded"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }
}
?>
