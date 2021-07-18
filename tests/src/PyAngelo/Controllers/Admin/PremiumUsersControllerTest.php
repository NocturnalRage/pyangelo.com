<?php
namespace tests\src\PyAngelo\Controllers\Admin;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Admin\PremiumUsersController;

class PremiumUsersControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->personRepository = Mockery::mock('PyAngelo\Repositories\PersonRepository');
    $this->avatar = Mockery::mock('Framework\Contracts\AvatarContract');
    $this->controller = new PremiumUsersController (
      $this->request,
      $this->response,
      $this->auth,
      $this->personRepository,
      $this->avatar
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Admin\PremiumUsersController');
  }

  /**
   * @runInSeparateProcess
   */
  public function testPremiumUsersWhenNotAdmin() {
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
  public function testPremiumUsersWhenAdmin() {
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->personRepository->shouldReceive('getPremiumMembers')->once()->with()->andReturn([]);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'admin/premium-users.html.php';
    $expectedPageTitle = 'Premium Users';
    $expectedMetaDescription = "This page shows the current PyAngelo Premium users.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
