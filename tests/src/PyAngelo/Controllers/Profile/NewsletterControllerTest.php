<?php
namespace tests\src\PyAngelo\Controllers\Profile; 
use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Profile\NewsletterController;
use PyAngelo\Auth\Auth;

class NewsletterControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->personRepository = Mockery::mock('PyAngelo\Repositories\PersonRepository');
    $this->controller = new NewsletterController (
      $this->request,
      $this->response,
      $this->auth,
      $this->personRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Profile\NewsletterController');
  }

  public function testRedirectsToLoginPageWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'You must be logged in to update your email newsletter settings.';
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testSuccessNewsletterController() {
    $listId = 1;
    $personId = 100;
    $subscriberStatusId = 1;
    $subscriber = [
      'list_id' => $listId,
      'person_id' => $personId,
      'subscriber_status_id' => $subscriberStatusId
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->personRepository->shouldReceive('getSubscriber')
      ->once()
      ->with($listId, $personId)
      ->andReturn($subscriber);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/newsletter.html.php';
    $expectedPageTitle = 'Email Newsletter Settings';
    $expectedMetaDescription = "Update your PyAngelo email newsletter settings.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
