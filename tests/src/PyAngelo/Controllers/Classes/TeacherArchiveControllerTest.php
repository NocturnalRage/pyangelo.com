<?php
namespace Tests\src\PyAngelo\Controllers\Classes;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Classes\TeacherArchiveController;

class TeacherArchiveControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->classRepository = Mockery::mock('PyAngelo\Repositories\ClassRepository');
    $this->controller = new TeacherArchiveController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Classes\TeacherArchiveController');
  }

  public function testTeacherArchiveControllerWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $expectedFlashMessage = "You must be logged in to archive a class!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testTeacherArchiveControllerWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher'));
    $expectedFlashMessage = "Please archive classes from the PyAngelo website!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testTeacherArchiveControllerWhenNoClassId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher'));
    $expectedFlashMessage = "You must select a class to archive";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testTeacherArchiveControllerWhenInvalidClass() {
    $classId = 99;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->classRepository->shouldReceive('getClassById')->once()->with($classId)->andReturn();
    $this->request->post["classId"] = $classId;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher'));
    $expectedFlashMessage = "You must select a valid class to archive!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testTeacherArchiveControllerWhenNotOwner() {
    $classId = 99;
    $personId = 109;
    $ownerId = 110;
    $class = [
      'class_id' => $classId,
      'person_id' => $ownerId,
      'class_name' => 'My Class'
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->classRepository->shouldReceive('getClassById')->once()->with($classId)->andReturn($class);
    $this->request->post["classId"] = $classId;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher'));
    $expectedFlashMessage = "You must be the owner of the class to archive it.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testTeacherArchiveControllerFailToArchive() {
    $classId = 99;
    $personId = 109;
    $ownerId = 109;
    $class = [
      'class_id' => $classId,
      'person_id' => $ownerId,
      'class_name' => 'My Class'
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->classRepository->shouldReceive('getClassById')->once()->with($classId)->andReturn($class);
    $this->classRepository->shouldReceive('archiveClass')->once()->with($classId)->andReturn(0);
    $this->request->post["classId"] = $classId;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher'));
    $expectedFlashMessage = "Sorry, we could not archive the class.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testTeacherArchiveControllerSuccess() {
    $classId = 99;
    $personId = 109;
    $ownerId = 109;
    $class = [
      'class_id' => $classId,
      'person_id' => $ownerId,
      'class_name' => 'My Class'
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->classRepository->shouldReceive('getClassById')->once()->with($classId)->andReturn($class);
    $this->classRepository->shouldReceive('archiveClass')->once()->with($classId)->andReturn(1);
    $this->request->post["classId"] = $classId;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher'));
    $expectedFlashMessage = "Your class has been archived.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }
}
?>
