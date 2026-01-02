<?php
namespace Tests\src\PyAngelo\Controllers;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\ContactPageController;

class ContactPageControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $recaptchaKey;
  protected $controller;


  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->recaptchaKey = "recaptcha";
    $this->controller = new ContactPageController (
      $this->request,
      $this->response,
      $this->auth,
      $this->recaptchaKey
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testContactPageControllerClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\ContactPageController');
  }
  public function testViewHasBeenSet() {
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'contact.html.php';
    $this->assertSame($expectedViewName, $this->response->getView());
  }

  public function testViewMetaDataHasBeenSet() {
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedPageTitle = "Contact Us";
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $expectedMetaDescription = "Contact us if you want to know something about PyAngelo.";
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
  public function testPersonDetailsForViewHasBeenSet() {
    $details = [
      'loggedIn' => false,
      'person' => ['person_id' => 0, 'email' => 'fred@hotmail.com'],
      'isAdmin' => false
    ];
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with()->andReturn($details);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $this->assertSame($details, $responseVars['personInfo']);
  }
}
?>
