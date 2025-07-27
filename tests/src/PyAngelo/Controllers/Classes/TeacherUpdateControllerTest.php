<?php
namespace Tests\src\PyAngelo\Controllers\Classes;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Classes\TeacherUpdateController;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class TeacherUpdateControllerTest extends TestCase {
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
    $this->controller = new TeacherUpdateController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Classes\TeacherUpdateController');
  }

  public function testTeacherUpdateControllerWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $expectedFlashMessage = "You must be logged in to update your classes!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testTeacherUpdateControllerWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher'));
    $expectedFlashMessage = "Please update your classes from the PyAngelo website!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testTeacherUpdateControllerWhenNoClassId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testTeacherUpdateControllerWhenInvalidClass() {
    $classId = 99;
    $this->request->post['classId'] = $classId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->classRepository->shouldReceive('getClassById')->once()->with($classId)->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testTeacherUpdateControllerWhenNotOwner() {
    $classId = 99;
    $personId = 109;
    $ownerId = 110;
    $this->request->post['classId'] = $classId;
    $class = [
      'class_id' => $classId,
      'person_id' => $ownerId,
      'class_name' => 'My Class'
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->classRepository->shouldReceive('getClassById')->once()->with($classId)->andReturn($class);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher'));
    $expectedFlashMessage = "You must be the owner of the class to update it.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  #[RunInSeparateProcess]
  public function testTeacherUpdateControllerWhenNoClassName() {
    session_start();
    $classId = 99;
    $personId = 109;
    $ownerId = 109;
    $this->request->post['classId'] = $classId;
    $class = [
      'class_id' => $classId,
      'person_id' => $ownerId,
      'class_name' => 'My Class'
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->classRepository->shouldReceive('getClassById')->once()->with($classId)->andReturn($class);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher/' . $classId . '/edit'));
    $expectedFlashMessage = "There were some errors. Please fix these below and then submit your changes again.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
    $this->assertSame("You must supply a name for your class.", $_SESSION['errors']['class_name']);
  }

  #[RunInSeparateProcess]
  public function testTeacherUpdateControllerWhenClassNameTooLong() {
    session_start();
    $classId = 99;
    $personId = 109;
    $ownerId = 109;
    $this->request->post['classId'] = $classId;
    $this->request->post['class_name'] = 'Longer than 100 xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
    $class = [
      'class_id' => $classId,
      'person_id' => $ownerId,
      'class_name' => 'My Class'
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->classRepository->shouldReceive('getClassById')->once()->with($classId)->andReturn($class);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher/' . $classId . '/edit'));
    $expectedFlashMessage = "There were some errors. Please fix these below and then submit your changes again.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
    $this->assertSame("The class name must be no more than 100 characters.", $_SESSION['errors']['class_name']);
  }

  #[RunInSeparateProcess]
  public function testTeacherUpdateControllerUnableToUpdate() {
    session_start();
    $classId = 99;
    $personId = 109;
    $ownerId = 109;
    $this->request->post['classId'] = $classId;
    $this->request->post['class_name'] = 'Valid Class Name';
    $class = [
      'class_id' => $classId,
      'person_id' => $ownerId,
      'class_name' => 'My Class'
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->classRepository->shouldReceive('getClassById')->once()->with($classId)->andReturn($class);
    $this->classRepository
         ->shouldReceive('updateClass')
         ->once()
         ->with($classId, $this->request->post['class_name'])
         ->andReturn(0);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher/' . $classId));
    $expectedFlashMessage = "We could not update the class name.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  #[RunInSeparateProcess]
  public function testTeacherUpdateControllerSuccess() {
    session_start();
    $classId = 99;
    $personId = 109;
    $ownerId = 109;
    $this->request->post['classId'] = $classId;
    $this->request->post['class_name'] = 'Valid Class Name';
    $class = [
      'class_id' => $classId,
      'person_id' => $ownerId,
      'class_name' => 'My Class'
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->classRepository->shouldReceive('getClassById')->once()->with($classId)->andReturn($class);
    $this->classRepository
         ->shouldReceive('updateClass')
         ->once()
         ->with($classId, $this->request->post['class_name'])
         ->andReturn(1);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher/' . $classId));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
