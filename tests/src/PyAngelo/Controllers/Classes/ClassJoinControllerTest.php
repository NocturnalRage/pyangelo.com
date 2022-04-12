<?php
namespace Tests\src\PyAngelo\Controllers\Classes;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Classes\ClassJoinController;

class ClassJoinControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->classRepository = Mockery::mock('PyAngelo\Repositories\ClassRepository');
    $this->controller = new ClassJoinController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Classes\ClassJoinController');
  }

  public function testClassJoinControllerWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $expectedFlashMessage = "You must be logged in to join a class.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testClassJoinControllerWhenNoJoinCode() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You need a class code to be able to join it.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testClassJoinControllerWhenInvalidJoinCode() {
    $joinCode = 'invalid-join-code';
    $this->request->get['joinCode'] = $joinCode;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->classRepository->shouldReceive('getClassByCode')->once()->with($joinCode)->andReturn();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "There is no such class to join.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testClassJoinControllerWhenCannotJoin() {
    $classId = 99;
    $personId = 299;
    $joinCode = 'valid-join-code';
    $class = [
      'class_id' => $classId
    ];
    $this->request->get['joinCode'] = $joinCode;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->classRepository->shouldReceive('getClassByCode')->once()->with($joinCode)->andReturn($class);
    $this->classRepository->shouldReceive('joinClass')->once()->with($classId, $personId)->andReturn(0);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "We could not enrol you in the class.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testClassJoinControllerSuccess() {
    $classId = 99;
    $personId = 299;
    $joinCode = 'valid-join-code';
    $class = [
      'class_id' => $classId
    ];
    $this->request->get['joinCode'] = $joinCode;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->classRepository->shouldReceive('getClassByCode')->once()->with($joinCode)->andReturn($class);
    $this->classRepository->shouldReceive('joinClass')->once()->with($classId, $personId)->andReturn(1);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/student'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }
}
?>
