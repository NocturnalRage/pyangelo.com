<?php
namespace Tests\src\PyAngelo\Controllers\AskTheTeacher;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\AskTheTeacher\AskTheTeacherEditController;

class AskTheTeacherEditControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->questionRepository = Mockery::mock('PyAngelo\Repositories\QuestionRepository');
    $this->controller = new AskTheTeacherEditController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\AskTheTeacher\AskTheTeacherEditController');
  }


  public function testRedirectToLoginPageWhenNotAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testControllerNoSlug() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testControllerInvalidSlug() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $slug = 'invalid-slug';
    $this->request->get['slug'] = $slug;
    $this->questionRepository->shouldReceive('getQuestionBySlug')->once()->with($slug)->andReturn(NULL);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  /**
   * @runInSeparateProcess
   */
  public function testControllerValidSlug() {
    session_start();
    $slug = 'valid-slug';
    $questionTitle = "My Question";
    $this->request->get['slug'] = $slug;
    $question = [
      'question_id' => 1,
      'question_title' => $questionTitle
    ];
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->questionRepository->shouldReceive('getQuestionBySlug')->once()->with($slug)->andReturn($question);
    $this->questionRepository->shouldReceive('getAllQuestionTypes')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/edit.html.php';
    $expectedPageTitle = 'Answer Question';
    $expectedMetaDescription = "Answer the " . $questionTitle . " question.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
