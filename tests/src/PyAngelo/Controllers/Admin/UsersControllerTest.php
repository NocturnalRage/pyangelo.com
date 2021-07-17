<?php
namespace tests\src\PyAngelo\Controllers\Admin;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Admin\UsersController;

class UsersControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->controller = new UsersController (
      $this->request,
      $this->response,
      $this->auth
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Admin\UsersController');
  }

  /**
   * @runInSeparateProcess
   */
  public function testUsersWhenNotAdmin() {
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testUsersWhenAdmin() {
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'admin/users.html.php';
    $expectedPageTitle = 'User Search';
    $expectedMetaDescription = "Search for a PyAngelo user.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
