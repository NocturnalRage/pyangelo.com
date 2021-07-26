<?php
namespace tests\src\PyAngelo\Controllers\Profile;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Profile\InvoicesController;

class InvoicesControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->personRepository = Mockery::mock('PyAngelo\Repositories\PersonRepository');
    $this->numberFormatter = Mockery::mock('\NumberFormatter');
    $this->controller = new InvoicesController(
      $this->request,
      $this->response,
      $this->auth,
      $this->personRepository,
      $this->numberFormatter
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Profile\InvoicesController');
  }

  public function testInvoicesControllerWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $expectedFlashMessage = "You must be logged in to view your invoices.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testInovicesControllerWhenLoggedInNoPayments() {
    $personId = 99;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->personRepository->shouldReceive('getPaymentHistory')
      ->once()
      ->with($personId)
      ->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/invoices.html.php';
    $expectedPageTitle = 'PyAngelo Invoices';
    $expectedMetaDescription = "Your payment history with PyAngelo.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
