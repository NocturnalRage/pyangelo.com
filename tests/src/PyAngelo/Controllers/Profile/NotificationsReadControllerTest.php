<?php
namespace Tests\src\PyAngelo\Controllers\Profile;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Profile\NotificationsReadController;

class NotificationsReadControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $personRepository;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->personRepository = Mockery::mock('PyAngelo\Repositories\PersonRepository');
    $this->controller = new NotificationsReadController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Profile\NotificationsReadController');
  }

  public function testCannotUpdateWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/notification-read.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must be logged in to mark a notification as read.', $responseVars['message']);
  }

  public function testErrorWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/notification-read.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must perform this action from the PyAngelo website.', $responseVars['message']);
  }

  public function testCannotUpdateWhenNoNotificationId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/notification-read.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must select a notification to mark as read.', $responseVars['message']);
  }

  public function testSuccessfullyUpdatesNotificationAsRead() {
    $notificationId = 100;
    $personId = 900;
    $notification = [
      'notification_id' => $notificationId,
      'person_id' => $personId
    ];
    $this->request->post['notificationId'] = $notificationId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->personRepository
      ->shouldReceive('getNotificationById')
      ->once()
      ->with($personId, $notificationId)
      ->andReturn($notification);
    $this->personRepository
      ->shouldReceive('markNotificationAsRead')
      ->once()
      ->with($personId, $notificationId);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/notification-read.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('success', $responseVars['status']);
    $this->assertSame('The notification has been marked as read.', $responseVars['message']);
  }
}
?>
