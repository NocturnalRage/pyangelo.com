<?php
namespace Tests\src\PyAngelo\Controllers\Quizzes;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Quizzes\QuizzesCreateController;

class QuizzesCreateControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->controller = new QuizzesCreateController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Quizzes\QuizzesCreateController');
  }

  public function testQuizzesCreateWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testQuizzesCreateWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testWhenNoTutorialSlug() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /page-not-found';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testWhenNotValidTutorial() {
    $slug = 'a-tutorial';
    $this->request->post = [
      'slug' => $slug
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialBySlug')->once()->with($slug)->andReturn();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /page-not-found';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testWhenNoQuiz() {
    $personId = 99;
    $slug = 'a-tutorial';
    $this->request->post = [
      'slug' => $slug
    ];
    $tutorialId = 1;
    $tutorial = [
      'tutorial_id' => $tutorialId,
      'slug' => $slug
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->tutorialRepository->shouldReceive('getTutorialBySlug')->once()->with($slug)->andReturn($tutorial);
    $this->tutorialRepository->shouldReceive('getUncompletedTutorialQuiz')->once()->with($tutorialId, $personId)->andReturn();
    $this->tutorialRepository->shouldReceive('getAllTutorialQuestions')->once()->with($tutorialId)->andReturn([]);
    $this->tutorialRepository->shouldReceive('createTutorialQuiz')->once()->with($tutorialId, $personId)->andReturn();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /tutorials/' . $slug . '/quizzes';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testWhenExistingQuiz() {
    $personId = 99;
    $slug = 'a-tutorial';
    $this->request->post = [
      'slug' => $slug
    ];
    $tutorialId = 1;
    $tutorial = [
      'tutorial_id' => $tutorialId,
      'slug' => $slug
    ];
    $tutorialQuizId = 199;
    $tutorialQuiz = [
      'tutorial_quiz_id' => $tutorialQuizId
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->tutorialRepository->shouldReceive('getTutorialBySlug')->once()->with($slug)->andReturn($tutorial);
    $this->tutorialRepository->shouldReceive('getUncompletedTutorialQuiz')->once()->with($tutorialId, $personId)->andReturn($tutorialQuiz);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /tutorials/' . $slug . '/quizzes';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
