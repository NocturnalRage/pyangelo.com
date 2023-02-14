<?php
namespace Tests\src\PyAngelo\Controllers\AskTheTeacher;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\AskTheTeacher\AskTheTeacherCommentController;

class AskTheTeacherCommentControllerTest extends TestCase {
  protected $questionRepository;
  protected $request;
  protected $response;
  protected $auth;
  protected $purifier;
  protected $avatar;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->questionRepository = Mockery::mock('PyAngelo\Repositories\QuestionRepository');
    $this->purifier = Mockery::mock('Framework\Contracts\PurifyContract');
    $this->avatar = Mockery::mock('Framework\Contracts\AvatarContract');
    $this->controller = new AskTheTeacherCommentController (
      $this->request,
      $this->response,
      $this->auth,
      $this->questionRepository,
      $this->purifier,
      $this->avatar
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\AskTheTeacher\AskTheTeacherCommentController');
  }

  public function testControllerWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/question-comment.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must be logged in to add a comment."';
    $expectedCommentHtml = '"We could not add your comment due to errors."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $this->assertSame($expectedCommentHtml, $responseVars['commentHtml']);
  }

  public function testQuestionCommentInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/question-comment.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"Please add a comment from the PyAngelo website."';
    $expectedCommentHtml = '"We could not add your comment due to errors."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $this->assertSame($expectedCommentHtml, $responseVars['commentHtml']);
  }

  public function testNoQuestionId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/question-comment.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"Please add a comment to a question."';
    $expectedCommentHtml = '"We could not add your comment due to errors."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $this->assertSame($expectedCommentHtml, $responseVars['commentHtml']);
  }

  public function testQuestionCommentNoComment() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->request->post['blogId'] = 1;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/question-comment.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"Please add a comment to a question."';
    $expectedCommentHtml = '"We could not add your comment due to errors."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $this->assertSame($expectedCommentHtml, $responseVars['commentHtml']);
  }

  public function testInvalidComment() {
    $personId = 99;
    $questionId = 1;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->questionRepository->shouldReceive('getQuestionById')->once()->with($questionId)->andReturn(NULL);
    $this->request->post['questionId'] = $questionId;
    $this->request->post['questionComment'] = 'Great comment';

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/question-comment.json.php';
    $expectedStatus = '"error"';
    $expectedMessage = '"You must add a comment to a valid question."';
    $expectedCommentHtml = '"We could not add your comment due to errors."';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $this->assertSame($expectedCommentHtml, $responseVars['commentHtml']);
  }

  public function testQuestionCommentValidComment() {
    $personId = 99;
    $email = 'fastfred@hotmail.com';
    $person = [
      'person_id' => $personId,
      'given_name' => 'Fast',
      'family_name' => 'Fred',
      'email' => $email
    ];
    $questionId = 1;
    $question = [
      'question_id' => $questionId,
      'slug' => 'great-question',
      'title' => 'A Great Question'
    ];
    $questionComment = 'Great question.';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('person')->times(4)->with()->andReturn($person);
    $this->questionRepository->shouldReceive('getQuestionById')->once()->with($questionId)->andReturn($question);
    $this->questionRepository->shouldReceive('insertQuestionComment')->once();
    $this->questionRepository->shouldReceive('updateQuestionLastUpdatedDate')->once()->with($questionId)->andReturn([]);
    $this->questionRepository->shouldReceive('getFollowers')->once()->andReturn([]);
    $this->purifier->shouldReceive('purify')->once()->with($questionComment)->andReturn($questionComment);
    $this->avatar->shouldReceive('getAvatarUrl')->twice()->with($email)->andReturn('avatar');
    $this->avatar->shouldReceive('setSizeInPixels')->once()->with(25);
    $this->request->post['questionId'] = $questionId;
    $this->request->post['questionComment'] = $questionComment;
    $this->request->server['REQUEST_SCHEME'] = 'https';
    $this->request->server['SERVER_NAME'] = 'www.pyangelo.com';

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/question-comment.json.php';
    $expectedStatus = '"success"';
    $expectedMessage = '"Your comment has been added."';
    $expectedCommentHtml = '"    <div class=\"media\">\n      <div class=\"media-left\">\n        <a href=\"#\">\n          <img class=\"media-object\" src=\"avatar\" alt=\"Fast Fred\" \/>\n        <\/a>\n      <\/div>\n      <div class=\"media-body\">\n        <h4 class=\"media-heading\">Fast Fred <small><i>Posted now<\/i><\/small><\/h4>\n        <p>Great question.<\/p>\n      <\/div>\n      <hr \/>\n    <\/div>"';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $this->assertSame($expectedCommentHtml, $responseVars['commentHtml']);
  }
}
?>
