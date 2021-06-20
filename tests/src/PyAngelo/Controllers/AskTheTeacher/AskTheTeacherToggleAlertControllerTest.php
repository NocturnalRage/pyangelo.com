<?php
namespace Tests\src\PyAngelo\Controllers\AskTheTeacher;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\AskTheTeacher\AskTheTeacherToggleAlertController;

class AskTheTeacherToggleAlertControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->questionRepository = Mockery::mock('PyAngelo\Repositories\QuestionRepository');
    $this->controller = new AskTheTeacherToggleAlertController (
      $this->request,
      $this->response,
      $this->auth,
      $this->questionRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\AskTheTeacher\AskTheTeacherToggleAlertController');
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('info', $responseVars['status']);
    $this->assertSame('Log in to update your notifications', $responseVars['message']);
  }

  public function testWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('Please update your notifications from the PyAngelo website.', $responseVars['message']);
  }

  public function testWhenNoQuestionId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must select a question to be notified about.', $responseVars['message']);
  }

  public function testWhenNotRealQuestion() {
    $this->request->post['questionId'] = 100;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->questionRepository
      ->shouldReceive('getQuestionById')
      ->once()
      ->with($this->request->post['questionId'])
      ->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must select a valid question to be notified about.', $responseVars['message']);
  }

  public function testToggleAlertWhenAlreadyAlerted() {
    $questionId = 100;
    $personId = 2;
    $question = [ 'question_id' => $questionId ];
    $this->request->post['questionId'] = $questionId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->questionRepository
      ->shouldReceive('getQuestionById')
      ->once()
      ->with($this->request->post['questionId'])
      ->andReturn($question);
    $this->questionRepository
      ->shouldReceive('shouldUserReceiveAlert')
      ->once()
      ->with($this->request->post['questionId'], $personId)
      ->andReturn($question);
    $this->questionRepository
      ->shouldReceive('removeFromQuestionAlert')
      ->once()
      ->with($this->request->post['questionId'], $personId);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('info', $responseVars['status']);
    $this->assertSame('Notifications are off for this question', $responseVars['message']);
  }

  public function testToggleAlertWhenNotAlerted() {
    $questionId = 100;
    $personId = 2;
    $question = [ 'question_id' => $questionId ];
    $this->request->post['questionId'] = $questionId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->questionRepository
      ->shouldReceive('getQuestionById')
      ->once()
      ->with($this->request->post['questionId'])
      ->andReturn($question);
    $this->questionRepository
      ->shouldReceive('shouldUserReceiveAlert')
      ->once()
      ->with($this->request->post['questionId'], $personId)
      ->andReturn(NULL);
    $this->questionRepository
      ->shouldReceive('addToQuestionAlert')
      ->once()
      ->with($this->request->post['questionId'], $personId);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/toggle-alert.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('success', $responseVars['status']);
    $this->assertSame('Notifications are on for this question', $responseVars['message']);
  }
}
?>
