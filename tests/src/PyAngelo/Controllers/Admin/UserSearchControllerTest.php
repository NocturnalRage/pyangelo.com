<?php
namespace tests\src\PyAngelo\Controllers\Admin;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Admin\UserSearchController;

class UserSearchControllerTest extends TestCase {
  protected $personRepository;
  protected $request;
  protected $response;
  protected $auth;
  protected $avatar;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->personRepository = Mockery::mock('PyAngelo\Repositories\PersonRepository');
    $this->avatar = Mockery::mock('Framework\Presentation\Gravatar');
    $this->controller = new UserSearchController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Admin\UserSearchController');
  }

  /**
   * @runInSeparateProcess
   */
  public function testUserSearchWhenNotAdmin() {
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testUserSearchWhenAdminNoParameters() {
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'admin/user-search.html.php';
    $expectedPageTitle = 'User Search Results';
    $expectedMetaDescription = "This page shows the users based on the search criteria.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testUserSearchWhenAdminWithSearchTerms() {
    $searchTerm = 'Jeff Plumb';
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->personRepository->shouldReceive('searchByNameAndEmail')
      ->once()
      ->with($searchTerm)
      ->andReturn([]);
    $this->request->get['search'] = $searchTerm;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'admin/user-search.html.php';
    $expectedPageTitle = 'User Search Results';
    $expectedMetaDescription = "This page shows the users based on the search criteria.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
