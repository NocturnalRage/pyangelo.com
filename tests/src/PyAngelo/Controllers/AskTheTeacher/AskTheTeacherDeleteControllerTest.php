<?php
namespace tests\src\PyAngelo\Controllers\AskTheTeacher;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\AskTheTeacher\AskTheTeacherDeleteController;

class AskTheTeacherDeleteControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->questionRepository = Mockery::mock('PyAngelo\Repositories\QuestionRepository');
    $this->controller = new AskTheTeacherDeleteController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\AskTheTeacher\AskTheTeacherDeleteController');
  }

  public function testAskTheTeacherDeleteControllerWhenNotAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testAskTheTeacherDeleteControllerInvalidCrsfToken() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /ask-the-teacher/question-list'));
    $expectedFlashMessage = "Please delete questions from the PyAngelo website!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testAskTheTeacherDeleteControllerWhenAdminWithNoFormData() {
    $flashMessage = 'You must select a question to delete';
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /ask-the-teacher/question-list'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($flashMessage, $this->request->session['flash']['message']);
  }
public function testAskTheTeacherDeleteControllerWhenAdminWithValidData() {
    $flashMessage = 'The question has been deleted.';
    $slug = 'my-question';
    $this->request->post['slug'] = $slug;
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->questionRepository->shouldReceive('deleteQuestion')->once()->with($slug)->andReturn(1);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /ask-the-teacher/question-list'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($flashMessage, $this->request->session['flash']['message']);
  }

  public function testAskTheTeacherDeleteControllerWhenAdminWithInvalidSlug() {
    $flashMessage = 'Sorry, we could not delete the question.';
    $slug = 'invalid-slug';
    $this->request->post['slug'] = $slug;
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->questionRepository->shouldReceive('deleteQuestion')->once()->with($slug)->andReturn(0);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /ask-the-teacher/question-list'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($flashMessage, $this->request->session['flash']['message']);
  }
}
?>
