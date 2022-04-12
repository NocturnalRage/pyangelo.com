<?php
namespace Tests\src\PyAngelo\Controllers\Classes;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Repositories\ClassRepository;
use PyAngelo\Controllers\Classes\TeacherIndexController;

class TeacherIndexControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->classRepository = Mockery::mock('PyAngelo\Repositories\ClassRepository');
    $this->controller = new TeacherIndexController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Classes\TeacherIndexController');
  }

  public function testRedirectToLoginPageWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('You must be logged in to view the classes you teach', $_SESSION['flash']['message']);
  }

  public function testTeacherClassesSuccess() {
    $allClasses = [
      [
        "class_id" => 1,
        "class_name" => "Class 1",
        "archived" => 0,
      ],
      [
        "class_id" => 2,
        "class_name" => "Class 2 Archived",
        "archived" => 1,
      ]
    ];
    $classes = array_filter($allClasses, function($class) {
      return ! $class['archived'];
    });
    $archivedClasses = array_filter($allClasses, function($class) {
      return $class['archived'];
    });
    $personId = 5;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->classRepository->shouldReceive('getTeacherClasses')->once()->with($personId)->andReturn($allClasses);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'classes/index.html.php';
    $expectedPageTitle = 'PyAngelo Classes I Teach';
    $expectedMetaDescription = "View all the classes I teach on PyAngelo.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
    $this->assertSame($classes, $responseVars['classes']);
    $this->assertSame($archivedClasses, $responseVars['archivedClasses']);
  }
}
?>
