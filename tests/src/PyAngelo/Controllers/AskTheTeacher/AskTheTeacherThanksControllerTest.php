<?php
namespace tests\src\PyAngelo\Controllers\AskTheTeacher;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\AskTheTeacher\AskTheTeacherThanksController;

class AskTheTeacherThanksControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->questionRepository = Mockery::mock('PyAngelo\Repositories\QuestionRepository');
    $this->purifier = Mockery::mock('Framework\Contracts\PurifyContract');
    $this->controller = new AskTheTeacherThanksController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\AskTheTeacher\AskTheTeacherThanksController');
  }

  public function testWhenNoQuestionId() {
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testWhenInvalidQuestionId() {
    $questionId = 999;
    $this->request->get['questionId'] = $questionId;
    $this->questionRepository->shouldReceive('getQuestionById')->once()->with($questionId)->andReturn(NULL);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testWhenValidQuestionId() {
    $questionId = 999;
    $questionTitle = 'My Question';
    $questiontext = 'What is it?';
    $question = [
      'question_id' => $questionId,
      'question_title' => $questionTitle,
      'question' => $questiontext
    ];
    $this->request->get['questionId'] = $questionId;
    $this->questionRepository->shouldReceive('getQuestionById')->once()->with($questionId)->andReturn($question);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/thanks.html.php';
    $expectedPageTitle = $questionTitle;
    $expectedMetaDescription = $questiontext;
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
