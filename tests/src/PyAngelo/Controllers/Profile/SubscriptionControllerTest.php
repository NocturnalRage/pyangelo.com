<?php
namespace tests\src\PyAngelo\Controllers\Profile;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Profile\SubscriptionController;

class SubscriptionControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->stripeRepository = Mockery::mock('PyAngelo\Repositories\StripeRepository');
    $this->numberFormatter = Mockery::mock('\NumberFormatter');
    $this->controller = new SubscriptionController(
      $this->request,
      $this->response,
      $this->auth,
      $this->stripeRepository,
      $this->numberFormatter
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Profile\SubscriptionController');
  }

  /**
   * @runInSeparateProcess
   */
  public function testSubscriptionControllerWhenNotLoggedIn() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $expectedFlashMessage = "You must be logged in to view your subscription information.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testSubscriptionControllerWhenLoggedInWithNoSubscription() {
    $personId = 99;
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->stripeRepository->shouldReceive('getCurrentSubscription')
      ->once()
      ->with($personId)
      ->andReturn(NULL);
    $this->stripeRepository->shouldReceive('getPastSubscriptions')
      ->once()
      ->with($personId)
      ->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/subscription.html.php';
    $expectedPageTitle = 'Subscription Information';
    $expectedMetaDescription = "This page lists any subscriptions you have with PyAngelo. You can update or cancel an existing subscription from this page.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testSubscriptionControllerWhenLoggedInWithMontlySubscription() {
    $testStripeId = 'TEST_STRIPE_ID';
    $personId = 99;
    $subscription = [
      'subscription_id' => 'SUB-1',
      'stripe_customer_id' => 'CUS-1',
      'stripe_plan_id' => 'Monthly_AUD_201701',
      'billing_period_in_months' => 1,
      'currency_code' => 'AUD',
      'start_date' => '2017-01-01 19:07:55',
      'current_period_end' => '2017-02-01 19:07:55'
    ];
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->stripeRepository->shouldReceive('getCurrentSubscription')
      ->once()
      ->with($personId)
      ->andReturn($subscription);
    $this->stripeRepository->shouldReceive('getPastSubscriptions')
      ->once()
      ->with($personId)
      ->andReturn(NULL);
    $this->request->env['STRIPE_CONNECT_USER_ID'] = $testStripeId;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/subscription.html.php';
    $expectedPageTitle = 'Subscription Information';
    $expectedMetaDescription = "This page lists any subscriptions you have with PyAngelo. You can update or cancel an existing subscription from this page.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
