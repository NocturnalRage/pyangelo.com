<?php
namespace tests\src\PyAngelo\Controllers\AskTheTeacher;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\AskTheTeacher\AskTheTeacherToggleFavouriteController;

class AskTheTeacherToggleFavouriteControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->questionRepository = Mockery::mock('PyAngelo\Repositories\QuestionRepository');
    $this->controller = new AskTheTeacherToggleFavouriteController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\AskTheTeacher\AskTheTeacherToggleFavouriteController');
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/toggle-favourite.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('info', $responseVars['status']);
    $this->assertSame('Log in to update your favourites', $responseVars['message']);
  }

  public function testWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/toggle-favourite.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('Please update your favourites from the PyAngelo website.', $responseVars['message']);
  }

  public function testWhenNoQuestionId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/toggle-favourite.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must select a question to favourite.', $responseVars['message']);
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
    $expectedViewName = 'ask-the-teacher/toggle-favourite.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must select a valid question to be favourite.', $responseVars['message']);
  }

  public function testToggleFavouriteWhenAlreadyFavourited() {
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
      ->shouldReceive('getQuestionFavourited')
      ->once()
      ->with($this->request->post['questionId'], $personId)
      ->andReturn($question);
    $this->questionRepository
      ->shouldReceive('removeFromQuestionFavourited')
      ->once()
      ->with($this->request->post['questionId'], $personId);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/toggle-favourite.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('info', $responseVars['status']);
    $this->assertSame('Question removed from favourites', $responseVars['message']);
  }

  public function testToggleFavouriteWhenNotFavouriteed() {
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
      ->shouldReceive('getQuestionFavourited')
      ->once()
      ->with($this->request->post['questionId'], $personId)
      ->andReturn(NULL);
    $this->questionRepository
      ->shouldReceive('addToQuestionFavourited')
      ->once()
      ->with($this->request->post['questionId'], $personId);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/toggle-favourite.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('success', $responseVars['status']);
    $this->assertSame('Question marked as a favourite', $responseVars['message']);
  }
}
?>
