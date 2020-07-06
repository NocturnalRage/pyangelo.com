<?php
namespace tests\src\PyAngelo\Controllers\Profile; 
use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Profile\NewsletterValidateController;
use PyAngelo\Auth\Auth;

class NewsletterValidateControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->personRepository = Mockery::mock('PyAngelo\Repositories\PersonRepository');
    $this->campaignRepository = Mockery::mock('PyAngelo\Repositories\CampaignRepository');
    $this->controller = new NewsletterValidateController (
      $this->request,
      $this->response,
      $this->auth,
      $this->personRepository,
      $this->campaignRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Profile\NewsletterValidateController');
  }

  public function testRedirectToLoginPageWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'You must be logged in to change your email newsletter preferences.';
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testRedirectToNewsletterWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /newsletter';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'Please update your preferences from the PyAngelo website.';
    $this->assertEquals($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testUpdateNewsletterUnsubscribe() {
    $lists = [
      [
        'list_id' => 1
      ],
      [
        'list_id' => 2
      ]
    ];
    $unsubscribedStatus = 2;
    $personId = 99;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->personRepository->shouldReceive('updateSubscriber')
      ->once()
      ->with(1, $personId, $unsubscribedStatus)
      ->andReturn($lists);
    $this->personRepository->shouldReceive('updateSubscriber')
      ->once()
      ->with(2, $personId, $unsubscribedStatus)
      ->andReturn($lists);
    $this->campaignRepository->shouldReceive('getAllLists')
      ->once()
      ->with()
      ->andReturn($lists);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /newsletter';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'Your preference has been updated.';
    $this->assertEquals($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testUpdateNewsletterSubscribe() {
    $lists = [
      [
        'list_id' => 1
      ],
      [
        'list_id' => 2
      ]
    ];
    $subscribedStatus = 1;
    $personId = 99;
    $this->request->post['newsletter'] = 'yes';
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->personRepository->shouldReceive('updateSubscriber')
      ->once()
      ->with(1, $personId, $subscribedStatus)
      ->andReturn($lists);
    $this->personRepository->shouldReceive('updateSubscriber')
      ->once()
      ->with(2, $personId, $subscribedStatus)
      ->andReturn($lists);
    $this->campaignRepository->shouldReceive('getAllLists')
      ->once()
      ->with()
      ->andReturn($lists);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedLocation = 'Location: /newsletter';
    $expectedHeaders = array(array('header', $expectedLocation));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = 'Your preference has been updated.';
    $this->assertEquals($expectedFlashMessage, $this->request->session['flash']['message']);
  }
}
?>
