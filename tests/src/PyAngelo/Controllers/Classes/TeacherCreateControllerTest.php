<?php
namespace Tests\src\PyAngelo\Controllers\Classes;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Classes\TeacherCreateController;

class TeacherCreateControllerTest extends TestCase {
  protected $classRepository;
  protected $request;
  protected $response;
  protected $auth;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->classRepository = Mockery::mock('PyAngelo\Repositories\ClassRepository');
    $this->controller = new TeacherCreateController (
      $this->request,
      $this->response,
      $this->auth,
      $this->classRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Classes\TeacherCreateController');
  }

  public function testTeacherCreateControllerWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $expectedFlashMessage = "You must be logged in to create a class";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testTeacherCreateControllerWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher'));
    $expectedFlashMessage = "You must create your class from the PyAngelo website.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testTeacherCreateControllerNoFormData() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher/new'));
    $expectedFlashMessage = "There were some errors. Please fix these below and then click the submit once more.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
    $this->assertSame("You must supply a name for your class.", $_SESSION['errors']['class_name']);
  }
  /**
   * @runInSeparateProcess
   */
  public function testTeacherCreateControllerWhenClassTooLong() {
    session_start();
    $this->request->post = [
      "class_name" => "This is more than 100 characters long xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher/new'));
    $expectedFlashMessage = "There were some errors. Please fix these below and then click the submit once more.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
    $this->assertSame("The class name must be no more than 100 characters.", $_SESSION['errors']['class_name']);
  }
  /**
   * @runInSeparateProcess
   */
  public function testTeacherCreateControllerWhenErrorCreatingClass() {
    session_start();
    $personId = 49;
    $this->request->post = [
      "class_name" => "Valid Class Name"
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->classRepository->shouldReceive('createNewClass')->once()->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher'));
    $expectedFlashMessage = "Something went wrong and we could not create your class.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }
  /**
   * @runInSeparateProcess
   */
  public function testTeacherCreateControllerSuccess() {
    session_start();
    $personId = 49;
    $classId = 9;
    $this->request->post = [
      "class_name" => "Valid Class Name"
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->classRepository->shouldReceive('createNewClass')->once()->andReturn($classId);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher/9'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
