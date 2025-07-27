<?php
namespace Tests\src\PyAngelo\Controllers\AskTheTeacher;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\AskTheTeacher\AskTheTeacherIndexController;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class AskTheTeacherIndexControllerTest extends TestCase {
  protected $questionRepository;
  protected $request;
  protected $response;
  protected $auth;
  protected $questionsPerPage;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->questionRepository = Mockery::mock('PyAngelo\Repositories\QuestionRepository');
    $this->questionsPerPage = 30;
    $this->controller = new AskTheTeacherIndexController (
      $this->request,
      $this->response,
      $this->auth,
      $this->questionRepository,
      $this->questionsPerPage
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\AskTheTeacher\AskTheTeacherIndexController');
  }

  #[RunInSeparateProcess]
  public function testWhenNoPageNo() {
    session_start();
    $this->request->server['REQUEST_URI'] = 'https://www.pyangelo.com';
    $this->questionRepository
         ->shouldReceive('getLatestQuestions')
         ->once()
         ->with(0, $this->questionsPerPage);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/index.html.php';
    $expectedPageTitle = 'Coding Questions Answered by Teachers';
    $expectedMetaDescription = "Ask the teacher a coding question.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
  }

  #[RunInSeparateProcess]
  public function testWhenPageNoEqual3() {
    session_start();
    $this->request->server['REQUEST_URI'] = 'https://www.pyangelo.com';
    $pageNo = 3;
    $this->request->get['pageNo'] = $pageNo;
    $offset = ($pageNo - 1) * $this->questionsPerPage;
    $this->questionRepository
         ->shouldReceive('getLatestQuestions')
         ->once()
         ->with($offset, $this->questionsPerPage);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/index.html.php';
    $expectedPageTitle = 'Coding Questions Answered by Teachers';
    $expectedMetaDescription = "Ask the teacher a coding question.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
  }
}
?>
