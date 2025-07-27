<?php
namespace tests\src\PyAngelo\Controllers\Admin;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Admin\UserController;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class UserControllerTest extends TestCase {
  protected $personRepository;
  protected $stripeRepository;
  protected $request;
  protected $response;
  protected $auth;
  protected $avatar;
  protected $numberFormatter;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->personRepository = Mockery::mock('PyAngelo\Repositories\PersonRepository');
    $this->avatar = Mockery::mock('Framework\Presentation\Gravatar');
    $this->stripeRepository = Mockery::mock('PyAngelo\Repositories\StripeRepository');
    $this->numberFormatter = Mockery::mock('\NumberFormatter');
    $this->controller = new UserController (
      $this->request,
      $this->response,
      $this->auth,
      $this->personRepository,
      $this->avatar,
      $this->stripeRepository,
      $this->numberFormatter
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Admin\UserController');
  }

  #[RunInSeparateProcess]
  public function testUserWhenNotAdmin() {
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /'));
    $expectedFlashMessage = "You are not authorised!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  #[RunInSeparateProcess]
  public function testUserWhenAdminNoPersonId() {
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  #[RunInSeparateProcess]
  public function testUserWhenAdminInvalidPersonId() {
    $personId = 100;
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->personRepository->shouldReceive('getPersonByIdForAdmin')
      ->once()
      ->with($personId)
      ->andReturn([]);
    $this->request->get['person_id'] = $personId;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /page-not-found'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
  }

  #[RunInSeparateProcess]
  public function testUserWhenAdminValidPersonId() {
    $personId = 100;
    $person = [
      'person_id' => $personId
    ];
    $payments = [];
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->personRepository->shouldReceive('getPersonByIdForAdmin')
      ->once()
      ->with($personId)
      ->andReturn($person);
    $this->personRepository->shouldReceive('getPaymentHistory')
      ->once()
      ->with($personId)
      ->andReturn($payments);
    $this->stripeRepository->shouldReceive('getCurrentSubscription')
      ->once()
      ->with($personId)
      ->andReturn();
    $this->stripeRepository->shouldReceive('getPastSubscriptions')
      ->once()
      ->with($personId)
      ->andReturn();
    $this->request->get['person_id'] = $personId;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'admin/user.html.php';
    $expectedPageTitle = 'Admin User Profile View';
    $expectedMetaDescription = "This page shows details of a person to a PyAngelo administrator.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
