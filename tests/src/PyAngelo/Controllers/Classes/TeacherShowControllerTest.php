<?php
namespace Tests\src\PyAngelo\Controllers\Classes;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Classes\TeacherShowController;

class TeacherShowControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->classRepository = Mockery::mock('PyAngelo\Repositories\ClassRepository');
    $this->controller = new TeacherShowController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Classes\TeacherShowController');
  }

  public function testTeacherShowControllerWhenNotLoggedIn() {
    $this->request->server['REQUEST_URI'] = "/classes/teacher";
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($this->request->server['REQUEST_URI'], $_SESSION['redirect']);
  }

  public function testTeacherShowControllerWhenNoClassId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testTeacherShowControllerWhenInvalidClassId() {
    $classId = 1;
    $this->request->get["classId"] = $classId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->classRepository->shouldReceive('getClassById')->once()->with($classId)->andReturn();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testTeacherShowControllerWhenNotOwner() {
    $personId = 5;
    $ownerId = 19;
    $classId = 1;
    $className = 'My Class';
    $class = [
      'class_id' => $classId,
      'class_name' => $className,
      'person_id' => $ownerId
    ];
    $this->request->get["classId"] = $classId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->classRepository->shouldReceive('getClassById')->once()->with($classId)->andReturn($class);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
  public function testTeacherShowControllerWhenOwner() {
    $personId = 5;
    $ownerId = 5;
    $classId = 1;
    $className = 'My Class';
    $students = [ 'person_id' => 100 ];
    $class = [
      'class_id' => $classId,
      'class_name' => $className,
      'person_id' => $ownerId
    ];
    $this->request->get["classId"] = $classId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->classRepository->shouldReceive('getClassById')->once()->with($classId)->andReturn($class);
    $this->classRepository->shouldReceive('getClassStudents')->once()->with($classId)->andReturn($students);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'classes/show.html.php';
    $expectedPageTitle = $className;
    $expectedMetaDescription = $className;
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
    $this->assertSame($students, $responseVars['students']);
  }
}
?>
