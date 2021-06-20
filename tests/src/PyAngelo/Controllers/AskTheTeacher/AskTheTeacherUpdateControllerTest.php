<?php
namespace Tests\src\PyAngelo\Controllers\AskTheTeacher;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\AskTheTeacher\AskTheTeacherUpdateController;

class AskTheTeacherUpdateControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->questionRepository = Mockery::mock('PyAngelo\Repositories\QuestionRepository');
    $this->avatar = Mockery::mock('Framework\Contracts\AvatarContract');
    $this->controller = new AskTheTeacherUpdateController (
      $this->request,
      $this->response,
      $this->auth,
      $this->avatar,
      $this->questionRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\AskTheTeacher\AskTheTeacherUpdateController');
  }

  public function testAskTheTeacherUpdateControllerWhenNotAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testAskTheTeacherUpdateControllerWhenNoQuestion() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testAskTheTeacherUpdateControllerWhenInvalidSlug() {
    $slug = 'invalid-slug';
    $this->request->post['slug'] = $slug; 
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->questionRepository->shouldReceive('getQuestionBySlug')->once()->with($slug)->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testAskTheTeacherUpdateControllerWhenInvalidCrsfToken() {
    $slug = 'valid-slug';
    $question = [
      'question_id' => 100,
      'slug' => $slug
    ];
    $this->request->post['slug'] = $slug; 
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->questionRepository->shouldReceive('getQuestionBySlug')->once()->with($slug)->andReturn($question);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "Please answer the question from the PyAngelo website!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testAskTheTeacherUpdateControllerWhenNoFormData() {
    session_start();
    $slug = 'valid-slug';
    $question = [
      'question_id' => 100,
      'slug' => $slug
    ];
    $errors = [
      'question_title' => 'You must supply a title for this question.',
      'question' => 'There must be a question.',
      'answer' => 'You must answer the question.',
      'question_type_id' => 'You must select the type of question.'
    ];
    $this->request->post['slug'] = $slug; 
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->questionRepository->shouldReceive('getQuestionBySlug')->once()->with($slug)->andReturn($question);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /ask-the-teacher/'. $slug . '/edit'));
    $expectedFlashMessage = "There were some errors. Please fix these below and then submit your answer again.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
    $this->assertSame($errors, $this->request->session['errors']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testAskTheTeacherUpdateControllerWithValidData() {
    session_start();
    $teacher = [
      'person_id' => 1
    ];
    $slug = 'valid-slug';
    $question = [
      'question_id' => 100,
      'question_title' => 'My Question',
      'slug' => $slug,
      'answered_at' => '2020-06-01 10:00:00',
      'teacher_id' => 1
    ];
    $this->request->post['slug'] = $slug; 
    $this->request->post['question_title'] = 'My Question'; 
    $this->request->post['question'] = 'What is it?'; 
    $this->request->post['answer'] = 'Fun.'; 
    $this->request->post['question_type_id'] = 1; 
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->questionRepository->shouldReceive('getQuestionBySlug')->once()->with($slug)->andReturn($question);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($teacher);
    $this->questionRepository
         ->shouldReceive('answerQuestion')
         ->once()
         ->with(
           $question['question_id'],
           $this->request->post['question_title'],
           $this->request->post['question'],
           $this->request->post['answer'],
           $this->request->post['question_type_id'],
           $teacher['person_id'],
           $slug,
           $question['answered_at']
         )
         ->andReturn(1);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /ask-the-teacher/'. $slug));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
