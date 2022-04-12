<?php
namespace Tests\src\PyAngelo\Controllers\Classes;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Classes\TeacherEditController;

class TeacherEditControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->classRepository = Mockery::mock('PyAngelo\Repositories\ClassRepository');
    $this->controller = new TeacherEditController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Classes\TeacherEditController');
  }

  public function testTeacherEditControllerWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $expectedFlashMessage = "You must be logged in to edit a class!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testTeacherEditControllerWhenNoClassId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testTeacherEditControllerWhenNoSuchClass() {
    $classId = 199;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->classRepository->shouldReceive('getClassById')
      ->once()
      ->with($classId)
      ->andReturn();
    $this->request->get['classId'] = $classId;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  public function testTeacherEditControllerWhenNotOwner() {
    $classId = 199;
    $personId = 50;
    $ownerId = 51;
    $classId = 9;
    $class = [
      'class_id' => $classId,
      'class_name' => 'My Class',
      'person_id' => $ownerId
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->classRepository->shouldReceive('getClassById')
      ->once()
      ->with($classId)
      ->andReturn($class);
    $this->request->get['classId'] = $classId;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /classes/teacher'));
    $expectedFlashMessage = "You must be the owner of the class to view it.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testTeacherEditControllerWhenOwner() {
    session_start();
    $personId = 50;
    $ownerId = 50;
    $classId = 9;
    $class = [
      'class_id' => $classId,
      'class_name' => 'My Class',
      'person_id' => $ownerId
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->classRepository->shouldReceive('getClassById')
      ->once()
      ->with($classId)
      ->andReturn($class);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->request->get['classId'] = $classId;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'classes/edit.html.php';
    $expectedPageTitle = 'Edit ' . $class['class_name'] . ' Class';
    $expectedMetaDescription = "Edit this class.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
