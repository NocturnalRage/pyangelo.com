<?php
namespace Tests\src\PyAngelo\Controllers\AskTheTeacher;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\AskTheTeacher\AskTheTeacherCategoryController;

class AskTheTeacherCategoryControllerTest extends TestCase {
  protected $questionRepository;
  protected $request;
  protected $response;
  protected $auth;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->questionRepository = Mockery::mock('PyAngelo\Repositories\QuestionRepository');
    $this->controller = new AskTheTeacherCategoryController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\AskTheTeacher\AskTheTeacherCategoryController');
  }

  public function testControllerWhenNoSlug() {
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testControllerWhenSlug() {
    $slug = 'category-slug';
    $this->request->get['slug'] = $slug;
    $this->questionRepository->shouldReceive('getCategoryBySlug')->once()->with($slug)->andReturn(NULL);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testControllerWhenValidSlug() {
    $slug = 'category-slug';
    $this->request->get['slug'] = $slug;
    $category = [
      'category_id' => 1,
      'description' => 'Category',
      'category_slug' => $slug
    ];
    $questions = [
      [
        'question_id' => 1,
        'slug' => 'a-question'
      ]
    ];
    $this->questionRepository->shouldReceive('getCategoryBySlug')->once()->with($slug)->andReturn($category);
    $this->questionRepository->shouldReceive('getCategoryQuestionsBySlug')->once()->with($slug)->andReturn($questions);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'ask-the-teacher/category.html.php';
    $expectedPageTitle = 'Coding Questions on Category';
    $expectedMetaDescription = "Coding questions on Category answered by teachers.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
