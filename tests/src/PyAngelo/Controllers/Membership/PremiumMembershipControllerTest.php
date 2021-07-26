<?php
namespace tests\src\PyAngelo\Controllers\Membership;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Membership\PremiumMembershipController;

class PremiumMembershipControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->stripeRepository = Mockery::mock('PyAngelo\Repositories\StripeRepository');
    $this->countryRepository = Mockery::mock('PyAngelo\Repositories\CountryRepository');
    $this->countryDetector = Mockery::mock('PyAngelo\Utilities\CountryDetector');
    $this->numberFormatter = Mockery::mock('\NumberFormatter');
    $this->controller = new PremiumMembershipController(
      $this->request,
      $this->response,
      $this->auth,
      $this->stripeRepository,
      $this->countryRepository,
      $this->countryDetector,
      $this->numberFormatter
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Membership\PremiumMembershipController');
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenNotLoggedIn() {
    session_start();
    $countryCode = 'US';
    $currencyCode = 'USD';
    $currency = [
      'currency_code' => $currencyCode
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->stripeRepository->shouldReceive('getMembershipPrices')->once()->with($currencyCode)->andReturn(false);
    $this->countryRepository->shouldReceive('getCurrencyFromCountryCode')->once()->with($countryCode)->andReturn($currency);
    $this->countryDetector->shouldReceive('getCountryFromIp')->once()->with()->andReturn($countryCode);

    $this->request->env['STRIPE_PUBLISHABLE_KEY'] = 'TEST-STRIPE-KEY';
    $this->request->server['REQUEST_URI'] = '/some-url';

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/premium-membership.html.php';
    $expectedPageTitle = 'Become a PyAngelo Premium Member';
    $expectedMetaDescription = 'Sign up to a subscription to become a premium member of the PyAngelo website. This will give you full access to every tutorial on the website.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenLoggedIn() {
    session_start();
    $countryCode = 'US';
    $currencyCode = 'USD';
    $person = [
      'person_id' => 1,
      'country_code' => $countryCode
    ];
    $currency = [
      'currency_code' => $currencyCode
    ];
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('hasActiveSubscription')->once()->with()->andReturn(false);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->stripeRepository->shouldReceive('getMembershipPrices')->once()->with($currencyCode)->andReturn(false);
    $this->countryRepository->shouldReceive('getCurrencyFromCountryCode')->once()->with($countryCode)->andReturn($currency);
    $this->request->env['STRIPE_PUBLISHABLE_KEY'] = 'TEST-STRIPE-KEY';
    $this->request->server['REQUEST_URI'] = '/some-url';

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'membership/premium-membership.html.php';
    $expectedPageTitle = 'Become a PyAngelo Premium Member';
    $expectedMetaDescription = 'Sign up to a subscription to become a premium member of the PyAngelo website. This will give you full access to every tutorial on the website.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
