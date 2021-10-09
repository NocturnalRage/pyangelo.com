<?php
namespace Tests\src\PyAngelo\Controllers\Quizzes;

use PHPUnit\Framework\TestCase;
use Mockery;
use Dotenv\Dotenv;
use Framework\Request;
use Framework\Response;
use PyAngelo\Repositories\TutorialRepository;
use PyAngelo\Controllers\Quizzes\QuizzesRecordCompletionController;

class QuizzesRecordCompletionControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->quizRepository = Mockery::mock('PyAngelo\Repositories\QuizRepository');
    $this->controller = new QuizzesRecordCompletionController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Quizzes\QuizzesRecordCompletionController');
  }

  public function testWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must complete the quiz from the PyAngelo website."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must be logged in to complete a quiz."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoQuizId() {
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must select a quiz to complete."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoQuizStartTime() {
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $quizId = 99;
    $this->request->post['quizId'] = $quizId;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"Did not receive the start time for the quiz."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoQuizEndTime() {
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $quizId = 99;
    $quizStartTime = '2021-09-25 22:08:00';
    $this->request->post['quizId'] = $quizId;
    $this->request->post['quizStartTime'] = $quizStartTime;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"Did not receive the end time for the quiz."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenInvalidQuiz() {
    $quizId = 99;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->quizRepository->shouldReceive('getQuizOptions')->once()->with($quizId)->andReturn();
    $this->request->post['quizId'] = $quizId;
    $this->request->post['quizStartTime'] = $quizStartTime;
    $this->request->post['quizEndTime'] = $quizStartTime;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must select a valid quiz to complete."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenDifferentPeople() {
    $personId = 150;
    $quizPersonId = 151;
    $quizId = 99;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
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
    $this->request->post['quizStartTime'] = $quizStartTime;
    $this->request->post['quizEndTime'] = $quizStartTime;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must select your own quiz to complete."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenCouldNotUpdateQuiz() {
    $personId = 150;
    $quizPersonId = 150;
    $quizId = 99;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['quizId'] = $quizId;
    $this->request->post['quizStartTime'] = $quizStartTime;
    $this->request->post['quizEndTime'] = $quizEndTime;
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
         ->shouldReceive('updateQuiz')
         ->once()
         ->with($quizId, $quizStartTime, $quizEndTime)
         ->andReturn(0);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"Could not record response."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoQuizResults() {
    $personId = 150;
    $quizPersonId = 150;
    $quizId = 99;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['quizId'] = $quizId;
    $this->request->post['quizStartTime'] = $quizStartTime;
    $this->request->post['quizEndTime'] = $quizEndTime;
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
         ->shouldReceive('updateQuiz')
         ->once()
         ->with($quizId, $quizStartTime, $quizEndTime)
         ->andReturn(1);
    $this->quizRepository->shouldReceive('getQuizResultsAndSkillMastery')->once()->with($quizId)->andReturn();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"Could not retrieve skills."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoSkillToAttemptedSkillQuiz() {
    $quizTypeId = 1; // Skill Quiz
    $personId = 150;
    $quizPersonId = 150;
    $quizId = 99;
    $skillId = 9;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['quizId'] = $quizId;
    $this->request->post['quizStartTime'] = $quizStartTime;
    $this->request->post['quizEndTime'] = $quizEndTime;
    $options = [
      [
        "person_id" => $quizPersonId
      ]
    ];
    $resultsSkillsMatrix = [
      [
        "skill_id" => $skillId,
        "mastery_level_id" => 0,
        "quiz_type_id" => $quizTypeId,
        "correct" => 1,
        "total" => 10,
      ]
    ];
    $newMastery = 1;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(2)->with()->andReturn($personId);
    $this->quizRepository->shouldReceive('getQuizOptions')->once()->with($quizId)->andReturn($options);
    $this->quizRepository
         ->shouldReceive('updateQuiz')
         ->once()
         ->with($quizId, $quizStartTime, $quizEndTime)
         ->andReturn(1);
    $this->quizRepository->shouldReceive('getQuizResultsAndSkillMastery')->once()->with($quizId)->andReturn($resultsSkillsMatrix);
    $this->quizRepository->shouldReceive('insertOrUpdateSkillMastery')->once()->with($skillId, $personId, $newMastery)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Completion recorded"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoSkillToAttemptedTutorialQuiz() {
    $quizTypeId = 2; // Tutorial Quiz
    $personId = 150;
    $quizPersonId = 150;
    $quizId = 99;
    $skillId = 9;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['quizId'] = $quizId;
    $this->request->post['quizStartTime'] = $quizStartTime;
    $this->request->post['quizEndTime'] = $quizEndTime;
    $options = [
      [
        "person_id" => $quizPersonId
      ]
    ];
    $resultsSkillsMatrix = [
      [
        "skill_id" => $skillId,
        "mastery_level_id" => 0,
        "quiz_type_id" => $quizTypeId,
        "correct" => 1,
        "total" => 10,
      ]
    ];
    $newMastery = 1;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(2)->with()->andReturn($personId);
    $this->quizRepository->shouldReceive('getQuizOptions')->once()->with($quizId)->andReturn($options);
    $this->quizRepository
         ->shouldReceive('updateQuiz')
         ->once()
         ->with($quizId, $quizStartTime, $quizEndTime)
         ->andReturn(1);
    $this->quizRepository->shouldReceive('getQuizResultsAndSkillMastery')->once()->with($quizId)->andReturn($resultsSkillsMatrix);
    $this->quizRepository->shouldReceive('insertOrUpdateSkillMastery')->once()->with($skillId, $personId, $newMastery)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Completion recorded"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenAttemptedToProficientSkillQuiz() {
    $quizTypeId = 1; // Skill Quiz
    $personId = 150;
    $quizPersonId = 150;
    $quizId = 99;
    $skillId = 9;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['quizId'] = $quizId;
    $this->request->post['quizStartTime'] = $quizStartTime;
    $this->request->post['quizEndTime'] = $quizEndTime;
    $options = [
      [
        "person_id" => $quizPersonId
      ]
    ];
    $resultsSkillsMatrix = [
      [
        "skill_id" => $skillId,
        "mastery_level_id" => 1,
        "quiz_type_id" => $quizTypeId,
        "correct" => 10,
        "total" => 10,
      ]
    ];
    $newMastery = 3;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(2)->with()->andReturn($personId);
    $this->quizRepository->shouldReceive('getQuizOptions')->once()->with($quizId)->andReturn($options);
    $this->quizRepository
         ->shouldReceive('updateQuiz')
         ->once()
         ->with($quizId, $quizStartTime, $quizEndTime)
         ->andReturn(1);
    $this->quizRepository->shouldReceive('getQuizResultsAndSkillMastery')->once()->with($quizId)->andReturn($resultsSkillsMatrix);
    $this->quizRepository->shouldReceive('insertOrUpdateSkillMastery')->once()->with($skillId, $personId, $newMastery)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Completion recorded"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenAttemptedToProficientTutorialQuiz() {
    $quizTypeId = 2; // Tutorial Quiz
    $personId = 150;
    $quizPersonId = 150;
    $quizId = 99;
    $skillId = 9;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['quizId'] = $quizId;
    $this->request->post['quizStartTime'] = $quizStartTime;
    $this->request->post['quizEndTime'] = $quizEndTime;
    $options = [
      [
        "person_id" => $quizPersonId
      ]
    ];
    $resultsSkillsMatrix = [
      [
        "skill_id" => $skillId,
        "mastery_level_id" => 1,
        "quiz_type_id" => $quizTypeId,
        "correct" => 10,
        "total" => 10,
      ]
    ];
    $newMastery = 3;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(2)->with()->andReturn($personId);
    $this->quizRepository->shouldReceive('getQuizOptions')->once()->with($quizId)->andReturn($options);
    $this->quizRepository
         ->shouldReceive('updateQuiz')
         ->once()
         ->with($quizId, $quizStartTime, $quizEndTime)
         ->andReturn(1);
    $this->quizRepository->shouldReceive('getQuizResultsAndSkillMastery')->once()->with($quizId)->andReturn($resultsSkillsMatrix);
    $this->quizRepository->shouldReceive('insertOrUpdateSkillMastery')->once()->with($skillId, $personId, $newMastery)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Completion recorded"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenProficientAllCorrectStaysProficientSkillQuiz() {
    $quizTypeId = 1; // Skill Quiz
    $personId = 150;
    $quizPersonId = 150;
    $quizId = 99;
    $skillId = 9;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['quizId'] = $quizId;
    $this->request->post['quizStartTime'] = $quizStartTime;
    $this->request->post['quizEndTime'] = $quizEndTime;
    $options = [
      [
        "person_id" => $quizPersonId
      ]
    ];
    $resultsSkillsMatrix = [
      [
        "skill_id" => $skillId,
        "mastery_level_id" => 3,
        "quiz_type_id" => $quizTypeId,
        "correct" => 10,
        "total" => 10,
      ]
    ];
    $newMastery = 3;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(2)->with()->andReturn($personId);
    $this->quizRepository->shouldReceive('getQuizOptions')->once()->with($quizId)->andReturn($options);
    $this->quizRepository
         ->shouldReceive('updateQuiz')
         ->once()
         ->with($quizId, $quizStartTime, $quizEndTime)
         ->andReturn(1);
    $this->quizRepository->shouldReceive('getQuizResultsAndSkillMastery')->once()->with($quizId)->andReturn($resultsSkillsMatrix);
    $this->quizRepository->shouldReceive('insertOrUpdateSkillMastery')->once()->with($skillId, $personId, $newMastery)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Completion recorded"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenProficientToMasteredTutorialQuiz() {
    $quizTypeId = 2; // Tutorial Quiz
    $personId = 150;
    $quizPersonId = 150;
    $quizId = 99;
    $skillId = 9;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['quizId'] = $quizId;
    $this->request->post['quizStartTime'] = $quizStartTime;
    $this->request->post['quizEndTime'] = $quizEndTime;
    $options = [
      [
        "person_id" => $quizPersonId
      ]
    ];
    $resultsSkillsMatrix = [
      [
        "skill_id" => $skillId,
        "mastery_level_id" => 3,
        "quiz_type_id" => $quizTypeId,
        "correct" => 10,
        "total" => 10,
      ]
    ];
    $newMastery = 4;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(2)->with()->andReturn($personId);
    $this->quizRepository->shouldReceive('getQuizOptions')->once()->with($quizId)->andReturn($options);
    $this->quizRepository
         ->shouldReceive('updateQuiz')
         ->once()
         ->with($quizId, $quizStartTime, $quizEndTime)
         ->andReturn(1);
    $this->quizRepository->shouldReceive('getQuizResultsAndSkillMastery')->once()->with($quizId)->andReturn($resultsSkillsMatrix);
    $this->quizRepository->shouldReceive('insertOrUpdateSkillMastery')->once()->with($skillId, $personId, $newMastery)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Completion recorded"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenMasteredToAttemptedSkillQuiz() {
    $quizTypeId = 1; // Skill Quiz
    $personId = 150;
    $quizPersonId = 150;
    $quizId = 99;
    $skillId = 9;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['quizId'] = $quizId;
    $this->request->post['quizStartTime'] = $quizStartTime;
    $this->request->post['quizEndTime'] = $quizEndTime;
    $options = [
      [
        "person_id" => $quizPersonId
      ]
    ];
    $resultsSkillsMatrix = [
      [
        "skill_id" => $skillId,
        "mastery_level_id" => 4,
        "quiz_type_id" => $quizTypeId,
        "correct" => 1,
        "total" => 10,
      ]
    ];
    $newMastery = 1;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(2)->with()->andReturn($personId);
    $this->quizRepository->shouldReceive('getQuizOptions')->once()->with($quizId)->andReturn($options);
    $this->quizRepository
         ->shouldReceive('updateQuiz')
         ->once()
         ->with($quizId, $quizStartTime, $quizEndTime)
         ->andReturn(1);
    $this->quizRepository->shouldReceive('getQuizResultsAndSkillMastery')->once()->with($quizId)->andReturn($resultsSkillsMatrix);
    $this->quizRepository->shouldReceive('insertOrUpdateSkillMastery')->once()->with($skillId, $personId, $newMastery)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Completion recorded"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenMasteredToAttemptedTutorialQuiz() {
    $quizTypeId = 2; // Tutorial Quiz
    $personId = 150;
    $quizPersonId = 150;
    $quizId = 99;
    $skillId = 9;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['quizId'] = $quizId;
    $this->request->post['quizStartTime'] = $quizStartTime;
    $this->request->post['quizEndTime'] = $quizEndTime;
    $options = [
      [
        "person_id" => $quizPersonId
      ]
    ];
    $resultsSkillsMatrix = [
      [
        "skill_id" => $skillId,
        "mastery_level_id" => 4,
        "quiz_type_id" => $quizTypeId,
        "correct" => 1,
        "total" => 10,
      ]
    ];
    $newMastery = 1;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(2)->with()->andReturn($personId);
    $this->quizRepository->shouldReceive('getQuizOptions')->once()->with($quizId)->andReturn($options);
    $this->quizRepository
         ->shouldReceive('updateQuiz')
         ->once()
         ->with($quizId, $quizStartTime, $quizEndTime)
         ->andReturn(1);
    $this->quizRepository->shouldReceive('getQuizResultsAndSkillMastery')->once()->with($quizId)->andReturn($resultsSkillsMatrix);
    $this->quizRepository->shouldReceive('insertOrUpdateSkillMastery')->once()->with($skillId, $personId, $newMastery)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Completion recorded"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenAttemptedToFamiliarSkillQuiz() {
    $quizTypeId = 1; // Skill Quiz
    $personId = 150;
    $quizPersonId = 150;
    $quizId = 99;
    $skillId = 9;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['quizId'] = $quizId;
    $this->request->post['quizStartTime'] = $quizStartTime;
    $this->request->post['quizEndTime'] = $quizEndTime;
    $options = [
      [
        "person_id" => $quizPersonId
      ]
    ];
    $resultsSkillsMatrix = [
      [
        "skill_id" => $skillId,
        "mastery_level_id" => 1,
        "quiz_type_id" => $quizTypeId,
        "correct" => 7,
        "total" => 10,
      ]
    ];
    $newMastery = 2;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(2)->with()->andReturn($personId);
    $this->quizRepository->shouldReceive('getQuizOptions')->once()->with($quizId)->andReturn($options);
    $this->quizRepository
         ->shouldReceive('updateQuiz')
         ->once()
         ->with($quizId, $quizStartTime, $quizEndTime)
         ->andReturn(1);
    $this->quizRepository->shouldReceive('getQuizResultsAndSkillMastery')->once()->with($quizId)->andReturn($resultsSkillsMatrix);
    $this->quizRepository->shouldReceive('insertOrUpdateSkillMastery')->once()->with($skillId, $personId, $newMastery)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Completion recorded"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenAttemptedToFamiliarTutorialQuiz() {
    $quizTypeId = 2; // Tutorial Quiz
    $personId = 150;
    $quizPersonId = 150;
    $quizId = 99;
    $skillId = 9;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['quizId'] = $quizId;
    $this->request->post['quizStartTime'] = $quizStartTime;
    $this->request->post['quizEndTime'] = $quizEndTime;
    $options = [
      [
        "person_id" => $quizPersonId
      ]
    ];
    $resultsSkillsMatrix = [
      [
        "skill_id" => $skillId,
        "mastery_level_id" => 1,
        "quiz_type_id" => $quizTypeId,
        "correct" => 7,
        "total" => 10,
      ]
    ];
    $newMastery = 2;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(2)->with()->andReturn($personId);
    $this->quizRepository->shouldReceive('getQuizOptions')->once()->with($quizId)->andReturn($options);
    $this->quizRepository
         ->shouldReceive('updateQuiz')
         ->once()
         ->with($quizId, $quizStartTime, $quizEndTime)
         ->andReturn(1);
    $this->quizRepository->shouldReceive('getQuizResultsAndSkillMastery')->once()->with($quizId)->andReturn($resultsSkillsMatrix);
    $this->quizRepository->shouldReceive('insertOrUpdateSkillMastery')->once()->with($skillId, $personId, $newMastery)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Completion recorded"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }
}
?>
