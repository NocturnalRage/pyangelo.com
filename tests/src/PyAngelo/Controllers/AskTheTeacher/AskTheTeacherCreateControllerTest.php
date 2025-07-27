<?php
namespace Tests\src\PyAngelo\Controllers\AskTheTeacher;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\AskTheTeacher\AskTheTeacherCreateController;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class AskTheTeacherCreateControllerTest extends TestCase {
  protected $questionRepository;
  protected $request;
  protected $response;
  protected $auth;
  protected $purifier;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->questionRepository = Mockery::mock('PyAngelo\Repositories\QuestionRepository');
    $this->purifier = Mockery::mock('Framework\Contracts\PurifyContract');
    $this->controller = new AskTheTeacherCreateController (
      $this->request,
      $this->response,
      $this->auth,
      $this->questionRepository,
      $this->purifier
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\AskTheTeacher\AskTheTeacherCreateController');
  }

  public function testAskTheTeacherCreateControllerWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $expectedFlashMessage = "You must be logged in to ask a question!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testAskTheTeacherCreateControllerInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "Please ask a question from the PyAngelo website!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  #[RunInSeparateProcess]
  public function testAskTheTeacherCreateControllerWhenAdminWithNoFormData() {
    session_start();
    $flashMessage = 'There were some errors. Please fix these below and then submit your question again.';
    $errors = [
      'question_title' => 'You must supply a title for this question.',
      'question' => 'You must ask a question.',
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /ask-the-teacher/ask'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($flashMessage, $_SESSION['flash']['message']);
    $this->assertSame($errors, $_SESSION['errors']);
  }

  #[RunInSeparateProcess]
  public function testAskTheTeacherCreateControllerWithValidData() {
    session_start();
    $this->request->server['REQUEST_SCHEME'] = "https";
    $this->request->server['SERVER_NAME'] = "www.pyangelo.com";

    $questionTitle = 'My Question';
    $question = 'What is it?';
    $slug = 'my-question';
    $questionId = 100;

    $personId = 99;
    $this->request->post = [
      'question_title' => $questionTitle,
      'question' => $question
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->questionRepository->shouldReceive('getQuestionBySlug')->once()->andReturn(NULL);
    $this->questionRepository->shouldReceive('createQuestion')->once()->with($personId, $questionTitle, $question, $slug)->andReturn($questionId);
    $this->purifier->shouldReceive('purify')->once()->with($questionTitle)->andReturn($questionTitle);
    $this->purifier->shouldReceive('purify')->once()->with($question)->andReturn($question);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /ask-the-teacher/thanks-for-your-question?questionId=' . $questionId));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
