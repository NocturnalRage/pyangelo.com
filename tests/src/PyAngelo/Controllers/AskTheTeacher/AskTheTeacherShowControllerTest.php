<?php
namespace Tests\src\PyAngelo\Controllers\AskTheTeacher;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\AskTheTeacher\AskTheTeacherShowController;

class AskTheTeacherShowControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->questionRepository = Mockery::mock('PyAngelo\Repositories\QuestionRepository');
    $this->purifier = Mockery::mock('Framework\Contracts\PurifyContract');
    $this->avatar = Mockery::mock('Framework\Contracts\AvatarContract');
    $this->showCommentCount = 10;
    $this->controller = new AskTheTeacherShowController (
      $this->request,
      $this->response,
      $this->auth,
      $this->questionRepository,
      $this->purifier,
      $this->avatar,
      $this->showCommentCount
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\AskTheTeacher\AskTheTeacherShowController');
  }

  public function testWhenNoSlug() {
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testWhenInvalidSlug() {
    $personId = 99;
    $slug = 'invalid-slug';
    $this->request->get['slug'] = $slug;
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->questionRepository->shouldReceive('getQuestionBySlugWithStatus')->once()->with($slug, $personId)->andReturn(NULL);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenValidSlugNotLoggedIn() {
    session_start();
    $this->request->server['REQUEST_URI'] = 'https://www.pyangelo.com';
    $personId = 0;
    $slug = 'valid-slug';
    $updatedAt = '2020-06-01 10:00:00';
    $questionId = 100;
    $questionTitle = 'My Question';
    $questiontext = 'What is it?';
    $question = [
      'question_id' => $questionId,
      'question_title' => $questionTitle,
      'question' => $questiontext,
      'updated_at' => $updatedAt
    ];
    $createdAt = '2020-07-01 11:00:00';
    $comments = [
      [
        'comment_id' => 1,
        'question_id' => $questionId,
        'comment' => 'comment',
        'created_at' => $createdAt,
      ]
    ];
    $this->request->get['slug'] = $slug;
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->questionRepository->shouldReceive('getQuestionBySlugWithStatus')->once()->with($slug, $personId)->andReturn($question);
    $this->questionRepository->shouldReceive('getNextQuestion')->once()->with($updatedAt);
    $this->questionRepository->shouldReceive('getPreviousQuestion')->once()->with($updatedAt);
    $this->questionRepository->shouldReceive('getPublishedQuestionComments')->once()->with($questionId)->andReturn($comments);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/show.html.php';
    $expectedPageTitle = $questionTitle;
    $expectedMetaDescription = $questiontext;
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenValidSlugLoggedIn() {
    session_start();
    $this->request->server['REQUEST_URI'] = 'https://www.pyangelo.com';
    $personId = 99;
    $slug = 'valid-slug';
    $updatedAt = '2020-06-01 10:00:00';
    $questionId = 100;
    $questionTitle = 'My Question';
    $questiontext = 'What is it?';
    $question = [
      'question_id' => $questionId,
      'question_title' => $questionTitle,
      'question' => $questiontext,
      'updated_at' => $updatedAt
    ];
    $createdAt = '2020-07-01 11:00:00';
    $comments = [
      [
        'comment_id' => 1,
        'question_id' => $questionId,
        'comment' => 'comment',
        'created_at' => $createdAt,
      ]
    ];
    $this->request->get['slug'] = $slug;
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->questionRepository->shouldReceive('getQuestionBySlugWithStatus')->once()->with($slug, $personId)->andReturn($question);
    $this->questionRepository->shouldReceive('getNextQuestion')->once()->with($updatedAt);
    $this->questionRepository->shouldReceive('getPreviousQuestion')->once()->with($updatedAt);
    $this->questionRepository->shouldReceive('getPublishedQuestionComments')->once()->with($questionId)->andReturn($comments);
    $this->questionRepository->shouldReceive('shouldUserReceiveAlert')->once()->with($questionId, $personId)->andReturn(1);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/show.html.php';
    $expectedPageTitle = $questionTitle;
    $expectedMetaDescription = $questiontext;
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
