<?php
namespace Tests\src\PyAngelo\Controllers\Classes;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Classes\TeacherSketchController;

class TeacherSketchControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->classRepository = Mockery::mock('PyAngelo\Repositories\ClassRepository');
    $this->controller = new TeacherSketchController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Classes\TeacherSketchController');
  }

  public function testTeacherSketchControllerWhenNotLoggedIn() {
    $this->request->server['REQUEST_URI'] = '/home';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $expectedFlashMessage = "You must be logged in to view a student's sketches";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testTeacherSketchControllerWhenNoClassId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testTeacherSketchControllerWhenNoPersonId() {
    $classId = 100;
    $this->request->get['classId'] = $classId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testTeacherSketchControllerWhenInvalidClass() {
    $studentId = 200;
    $classId = 100;
    $this->request->get['classId'] = $classId;
    $this->request->get['personId'] = $studentId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->classRepository->shouldReceive('getClassById')->once()->with($classId)->andReturn();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testTeacherSketchControllerWhenNotOwner() {
    $personId = 1;
    $ownerId = 2;
    $studentId = 200;
    $classId = 100;
    $class = [
      'class_id' => $classId,
      'person_id' => $ownerId,
      'class_name' => 'My Class'
    ];
    $this->request->get['classId'] = $classId;
    $this->request->get['personId'] = $studentId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->classRepository->shouldReceive('getClassById')->once()->with($classId)->andReturn($class);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher'));
    $expectedFlashMessage = "You must be the owner of a class to view the student's sketches.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testTeacherSketchControllerWhenOwner() {
    $personId = 1;
    $ownerId = 1;
    $studentId = 200;
    $classId = 100;
    $class = [
      'class_id' => $classId,
      'person_id' => $ownerId,
      'class_name' => 'My Class'
    ];
    $student = [
      'person_id' => $studentId,
      'given_name' => 'Master',
      'family_name' => 'Coder'
    ];
    $sketches = [];
    $this->request->get['classId'] = $classId;
    $this->request->get['personId'] = $studentId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->classRepository->shouldReceive('getClassById')->once()->with($classId)->andReturn($class);
    $this->classRepository->shouldReceive('getStudentFromClass')->once()->with($classId, $studentId)->andReturn($student);
    $this->classRepository->shouldReceive('getStudentSketches')->once()->with($classId, $studentId)->andReturn($sketches);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'classes/student-sketches.html.php';
    $expectedPageTitle = 'Sketches - ' . $student['given_name'] . ' ' . $student['family_name'];
    $expectedMetaDescription = 'The sketches of ' . $student['given_name'] . ' ' . $student['family_name'];
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
