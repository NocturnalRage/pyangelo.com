<?php
namespace Tests\src\PyAngelo\Controllers\AskTheTeacher;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\AskTheTeacher\AskTheTeacherMyQuestionsController;

class AskTheTeacherMyQuestionsControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->questionRepository = Mockery::mock('PyAngelo\Repositories\QuestionRepository');
    $this->questionsPerPage = 30;
    $this->controller = new AskTheTeacherMyQuestionsController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\AskTheTeacher\AskTheTeacherMyQuestionsController');
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenNotLoggedIn() {
    session_start();
    $this->request->server['REQUEST_URI'] = 'https://www.pyangelo.com';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $expectedFlashMessage = "You must be logged in to view your questions!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenLoggedIn() {
    session_start();
    $personId = 100;
    $this->request->server['REQUEST_URI'] = 'https://www.pyangelo.com';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->questionRepository
         ->shouldReceive('getQuestionsByPersonId')
         ->once()
         ->with($personId)
         ->andReturn([]);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/my-questions.html.php';
    $expectedPageTitle = 'My Questions';
    $expectedMetaDescription = "A list of all the questions I have asked.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
