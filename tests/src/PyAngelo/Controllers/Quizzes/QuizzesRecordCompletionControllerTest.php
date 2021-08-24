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
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->controller = new QuizzesRecordCompletionController (
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

  public function testWhenNoTutorialQuizId() {
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
    $tutorialQuizId = 99;
    $this->request->post['tutorialQuizId'] = $tutorialQuizId;
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
    $tutorialQuizId = 99;
    $quizStartTime = '2021-09-25 22:08:00';
    $this->request->post['tutorialQuizId'] = $tutorialQuizId;
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
    $tutorialQuizId = 99;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialQuizOptions')->once()->with($tutorialQuizId)->andReturn();
    $this->request->post['tutorialQuizId'] = $tutorialQuizId;
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
    $tutorialQuizId = 99;
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
    $this->tutorialRepository->shouldReceive('getTutorialQuizOptions')->once()->with($tutorialQuizId)->andReturn($options);
    $this->request->post['tutorialQuizId'] = $tutorialQuizId;
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
    $tutorialQuizId = 99;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['tutorialQuizId'] = $tutorialQuizId;
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
    $this->tutorialRepository->shouldReceive('getTutorialQuizOptions')->once()->with($tutorialQuizId)->andReturn($options);
    $this->tutorialRepository
         ->shouldReceive('updateTutorialQuiz')
         ->once()
         ->with($tutorialQuizId, $quizStartTime, $quizEndTime)
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
    $tutorialQuizId = 99;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['tutorialQuizId'] = $tutorialQuizId;
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
    $this->tutorialRepository->shouldReceive('getTutorialQuizOptions')->once()->with($tutorialQuizId)->andReturn($options);
    $this->tutorialRepository
         ->shouldReceive('updateTutorialQuiz')
         ->once()
         ->with($tutorialQuizId, $quizStartTime, $quizEndTime)
         ->andReturn(1);
    $this->tutorialRepository->shouldReceive('getQuizResultsAndSkillMastery')->once()->with($tutorialQuizId)->andReturn();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"Could not retrieve skills."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenNoSkillToAttempted() {
    $personId = 150;
    $quizPersonId = 150;
    $tutorialQuizId = 99;
    $skillId = 9;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['tutorialQuizId'] = $tutorialQuizId;
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
        "correct" => 1,
        "total" => 10,
      ]
    ];
    $newMastery = 1;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(3)->with()->andReturn($personId);
    $this->tutorialRepository->shouldReceive('getTutorialQuizOptions')->once()->with($tutorialQuizId)->andReturn($options);
    $this->tutorialRepository
         ->shouldReceive('updateTutorialQuiz')
         ->once()
         ->with($tutorialQuizId, $quizStartTime, $quizEndTime)
         ->andReturn(1);
    $this->tutorialRepository->shouldReceive('getQuizResultsAndSkillMastery')->once()->with($tutorialQuizId)->andReturn($resultsSkillsMatrix);
    $this->tutorialRepository->shouldReceive('getSkillMastery')->once()->with($skillId, $personId)->andReturn();
    $this->tutorialRepository->shouldReceive('insertSkillMastery')->once()->with($skillId, $personId, $newMastery)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Completion recorded"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenAttemptedToProficient() {
    $personId = 150;
    $quizPersonId = 150;
    $tutorialQuizId = 99;
    $skillId = 9;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['tutorialQuizId'] = $tutorialQuizId;
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
        "correct" => 10,
        "total" => 10,
      ]
    ];
    $newMastery = 3;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(3)->with()->andReturn($personId);
    $this->tutorialRepository->shouldReceive('getTutorialQuizOptions')->once()->with($tutorialQuizId)->andReturn($options);
    $this->tutorialRepository
         ->shouldReceive('updateTutorialQuiz')
         ->once()
         ->with($tutorialQuizId, $quizStartTime, $quizEndTime)
         ->andReturn(1);
    $this->tutorialRepository->shouldReceive('getQuizResultsAndSkillMastery')->once()->with($tutorialQuizId)->andReturn($resultsSkillsMatrix);
    $this->tutorialRepository->shouldReceive('getSkillMastery')->once()->with($skillId, $personId)->andReturn(1);
    $this->tutorialRepository->shouldReceive('updateSkillMastery')->once()->with($skillId, $personId, $newMastery)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Completion recorded"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenProficientToMastered() {
    $personId = 150;
    $quizPersonId = 150;
    $tutorialQuizId = 99;
    $skillId = 9;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['tutorialQuizId'] = $tutorialQuizId;
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
        "correct" => 10,
        "total" => 10,
      ]
    ];
    $newMastery = 4;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(3)->with()->andReturn($personId);
    $this->tutorialRepository->shouldReceive('getTutorialQuizOptions')->once()->with($tutorialQuizId)->andReturn($options);
    $this->tutorialRepository
         ->shouldReceive('updateTutorialQuiz')
         ->once()
         ->with($tutorialQuizId, $quizStartTime, $quizEndTime)
         ->andReturn(1);
    $this->tutorialRepository->shouldReceive('getQuizResultsAndSkillMastery')->once()->with($tutorialQuizId)->andReturn($resultsSkillsMatrix);
    $this->tutorialRepository->shouldReceive('getSkillMastery')->once()->with($skillId, $personId)->andReturn(1);
    $this->tutorialRepository->shouldReceive('updateSkillMastery')->once()->with($skillId, $personId, $newMastery)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Completion recorded"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenMasteredToAttempted() {
    $personId = 150;
    $quizPersonId = 150;
    $tutorialQuizId = 99;
    $skillId = 9;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['tutorialQuizId'] = $tutorialQuizId;
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
        "correct" => 1,
        "total" => 10,
      ]
    ];
    $newMastery = 1;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(3)->with()->andReturn($personId);
    $this->tutorialRepository->shouldReceive('getTutorialQuizOptions')->once()->with($tutorialQuizId)->andReturn($options);
    $this->tutorialRepository
         ->shouldReceive('updateTutorialQuiz')
         ->once()
         ->with($tutorialQuizId, $quizStartTime, $quizEndTime)
         ->andReturn(1);
    $this->tutorialRepository->shouldReceive('getQuizResultsAndSkillMastery')->once()->with($tutorialQuizId)->andReturn($resultsSkillsMatrix);
    $this->tutorialRepository->shouldReceive('getSkillMastery')->once()->with($skillId, $personId)->andReturn(1);
    $this->tutorialRepository->shouldReceive('updateSkillMastery')->once()->with($skillId, $personId, $newMastery)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'quizzes/complete.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Completion recorded"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenAttemptedToFamiliar() {
    $personId = 150;
    $quizPersonId = 150;
    $tutorialQuizId = 99;
    $skillId = 9;
    $quizStartTime = '2021-09-25 22:08:00';
    $quizEndTime = '2021-09-25 22:09:00';
    $this->request->post['tutorialQuizId'] = $tutorialQuizId;
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
        "correct" => 7,
        "total" => 10,
      ]
    ];
    $newMastery = 2;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->times(3)->with()->andReturn($personId);
    $this->tutorialRepository->shouldReceive('getTutorialQuizOptions')->once()->with($tutorialQuizId)->andReturn($options);
    $this->tutorialRepository
         ->shouldReceive('updateTutorialQuiz')
         ->once()
         ->with($tutorialQuizId, $quizStartTime, $quizEndTime)
         ->andReturn(1);
    $this->tutorialRepository->shouldReceive('getQuizResultsAndSkillMastery')->once()->with($tutorialQuizId)->andReturn($resultsSkillsMatrix);
    $this->tutorialRepository->shouldReceive('getSkillMastery')->once()->with($skillId, $personId)->andReturn(1);
    $this->tutorialRepository->shouldReceive('updateSkillMastery')->once()->with($skillId, $personId, $newMastery)->andReturn();

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
