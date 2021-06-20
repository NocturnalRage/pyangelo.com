<?php
namespace Tests\src\PyAngelo\Controllers\Profile;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Profile\UnsubscribeThreadController;

class UnsubscribeThreadControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->blogRepository = Mockery::mock('PyAngelo\Repositories\BlogRepository');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->controller = new UnsubscribeThreadController (
      $this->request,
      $this->response,
      $this->auth,
      $this->blogRepository,
      $this->tutorialRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Profile\UnsubscribeThreadController');
  }

  public function testCannotUpdateWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/unsubscribe-thread.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must be logged in to unsubscribe from a thread.', $responseVars['message']);
  }

  public function testErrorWhenInvalidCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/unsubscribe-thread.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must perform this action from the PyAngelo website.', $responseVars['message']);
  }

  public function testCannotUpdateWhenNoNotificationTypeId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/unsubscribe-thread.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must select a thread to unsubscribe from.', $responseVars['message']);
  }

  public function testCannotUpdateWhenNoNotificationType() {
    $this->request->post['notificationTypeId'] = 1;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/unsubscribe-thread.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must select a thread type to unsubscribe from.', $responseVars['message']);
  }

  public function testSuccessfullyUnsubscribeFromBlog() {
    $notificationTypeId = 1;
    $this->request->post['notificationTypeId'] = $notificationTypeId;
    $this->request->post['notificationType'] = 'blog';
    $notificationId = 100;
    $personId = 900;
    $notification = [
      'notification_id' => $notificationId,
      'person_id' => $personId
    ];
    $this->request->post['notificationId'] = $notificationId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->blogRepository
      ->shouldReceive('removeFromBlogAlert')
      ->once()
      ->with($notificationTypeId, $personId)
      ->andReturn($notification);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/unsubscribe-thread.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('success', $responseVars['status']);
    $this->assertSame('You will not receive any more notifications about this blog.', $responseVars['message']);
  }

  public function testSuccessfullyUnsubscribeFromLesson() {
    $notificationTypeId = 1;
    $this->request->post['notificationTypeId'] = $notificationTypeId;
    $this->request->post['notificationType'] = 'lesson';
    $notificationId = 100;
    $personId = 900;
    $notification = [
      'notification_id' => $notificationId,
      'person_id' => $personId
    ];
    $this->request->post['notificationId'] = $notificationId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->tutorialRepository
      ->shouldReceive('removeFromLessonAlert')
      ->once()
      ->with($notificationTypeId, $personId)
      ->andReturn($notification);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/unsubscribe-thread.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('success', $responseVars['status']);
    $this->assertSame('You will not receive any more notifications about this lesson.', $responseVars['message']);
  }

  public function testErrorWhenInvalidNotificationType() {
    $notificationTypeId = 1;
    $this->request->post['notificationTypeId'] = $notificationTypeId;
    $this->request->post['notificationType'] = 'not-valid-notification-type';
    $notificationId = 100;
    $personId = 900;
    $notification = [
      'notification_id' => $notificationId,
      'person_id' => $personId
    ];
    $this->request->post['notificationId'] = $notificationId;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/unsubscribe-thread.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('We did not know how to unsubscribe you from this thread.', $responseVars['message']);
  }
}
?>
