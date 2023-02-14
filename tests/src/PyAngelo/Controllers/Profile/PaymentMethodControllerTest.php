<?php
namespace tests\src\PyAngelo\Controllers\Profile;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Profile\PaymentMethodController;

class PaymentMethodControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->controller = new PaymentMethodController(
      $this->request,
      $this->response,
      $this->auth
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Profile\PaymentMethodController');
  }

  /**
   * @runInSeparateProcess
   */
  public function testPaymentMethodControllerWhenNotLoggedIn() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $expectedFlashMessage = "You must be logged in to update your payment method.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testPaymentMethodControllerWhenLoggedInWithNoSubscription() {
    $personId = 99;
    $person = [
      'person_id' => $personId,
      'stripe_customer_id' => '',
      'last4' => ''
    ];
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with();
    $this->request->env['STRIPE_PUBLISHABLE_KEY'] = 'TEST-STRIPE-KEY';

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/payment-method.html.php';
    $expectedPageTitle = 'Update Your Credit Card Details';
    $expectedMetaDescription = "This page allows you to update the credit card you have stored for your subscriptions.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testPaymentMethodControllerWhenLoggedInWithSubscription() {
    $personId = 99;
    $person = [
      'person_id' => $personId,
      'stripe_customer_id' => 'CUS-1',
      'last4' => '4242'
    ];
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with();
    $this->request->env['STRIPE_PUBLISHABLE_KEY'] = 'TEST-STRIPE-KEY';

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/payment-method.html.php';
    $expectedPageTitle = 'Update Your Credit Card Details';
    $expectedMetaDescription = "This page allows you to update the credit card you have stored for your subscriptions.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
