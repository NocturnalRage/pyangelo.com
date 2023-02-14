<?php
namespace tests\src\PyAngelo\Controllers\Admin;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Admin\StripeWebhookController;

class StripeWebhookControllerTest extends TestCase {
  protected $personRepository;
  protected $stripeRepository;
  protected $request;
  protected $response;
  protected $auth;
  protected $stripeWrapper;
  protected $stripeWebhookEmails;
  protected $numberFormatter;
  protected $controller;

  public function setUp(): void {
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../../../', '.env.test');
    $dotenv->load();
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->stripeWrapper = Mockery::mock('Framework\Billing\StripeWrapper');
    $this->stripeWebhookEmails = Mockery::mock('PyAngelo\Email\StripeWebhookEmails');
    $this->stripeRepository = Mockery::mock('PyAngelo\Repositories\StripeRepository');
    $this->personRepository = Mockery::mock('PyAngelo\Repositories\PersonRepository');
    $this->numberFormatter = Mockery::mock('\NumberFormatter');
    $this->controller = new StripeWebhookController(
      $this->request,
      $this->response,
      $this->auth,
      $this->stripeWrapper,
      $this->stripeWebhookEmails,
      $this->stripeRepository,
      $this->personRepository,
      $_ENV['STRIPE_WEBHOOK_SECRET'],
      $this->numberFormatter
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Admin\StripeWebhookController');
  }

  public function testTestWebhook() {
    $result = $this->controller->testWebhook();
    $this->assertSame(1, $result);
  }
}
?>
