<?php
namespace tests\src\PyAngelo\Controllers\Admin;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Admin\MetricsController;

class MetricsControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->metricRepository = Mockery::mock('PyAngelo\Repositories\MetricRepository');
    $this->controller = new MetricsController (
      $this->request,
      $this->response,
      $this->auth,
      $this->metricRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Admin\MetricsController');
  }

  /**
   * @runInSeparateProcess
   */
  public function testMetricsWhenNotAdmin() {
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
  public function testMetricsWhenAdmin() {
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->metricRepository->shouldReceive('getCountMetrics')->once();
    $this->metricRepository->shouldReceive('getSubscriberGrowthByMonth')->once();
    $this->metricRepository->shouldReceive('getSubscriberPaymentsByMonth')->once();
    $this->metricRepository->shouldReceive('getPremiumMemberCountByMonth')->once();
    $this->metricRepository->shouldReceive('getPremiumMemberCountByPlan')->once();
    $this->metricRepository->shouldReceive('getPremiumMemberCountByCountry')->once();
    $this->metricRepository->shouldReceive('getMemberCountByMonth')->once();
    $this->metricRepository->shouldReceive('getMemberCountByDay')->once();
    $this->metricRepository->shouldReceive('getMemberCountByCountry')->once();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'admin/metrics.html.php';
    $expectedPageTitle = 'PyAngelo Metrics';
    $expectedMetaDescription = "Track the progress of the PyAngelo website through metrics.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
