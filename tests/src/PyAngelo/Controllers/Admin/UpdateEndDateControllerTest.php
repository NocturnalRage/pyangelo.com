<?php
namespace tests\src\PyAngelo\Controllers\Admin;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Admin\UpdateEndDateController;

class UpdateEndDateControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->personRepository = Mockery::mock('PyAngelo\Repositories\PersonRepository');
    $this->controller = new UpdateEndDateController (
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
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Admin\UpdateEndDateController');
  }

  /**
   * @runInSeparateProcess
   */
  public function testUpdateEndDateWhenNotAdmin() {
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
  public function testUpdateEndDateWhenAdminNoPersonId() {
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /admin/users'));
    $expectedFlashMessage = "You must select a person in order to grant them premium member access!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testUpdateEndDateWhenAdminInvalidPersonId() {
    $personId = 100;
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->personRepository->shouldReceive('getPersonByIdForAdmin')
      ->once()
      ->with($personId)
      ->andReturn();

    $this->request->post['person_id'] = $personId;
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /admin/users'));
    $expectedFlashMessage = "You must select a valid person in order to grant them premium member access!";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testUpdateEndDateWhenAdminNoMonth() {
    $personId = 100;
    $person = [
      'person_id' => $personId
    ];
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->personRepository->shouldReceive('getPersonByIdForAdmin')
      ->once()
      ->with($personId)
      ->andReturn($person);

    $this->request->post['person_id'] = $personId;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /admin/users/' . $personId));
    $expectedFlashMessage = "You must select the number of months access you wish to grant.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testUpdateEndDateWhenAdminInvalidMonth() {
    $personId = 100;
    $months = 4;
    $person = [
      'person_id' => $personId
    ];
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->personRepository->shouldReceive('getPersonByIdForAdmin')
      ->once()
      ->with($personId)
      ->andReturn($person);
    $this->request->post['person_id'] = $personId;
    $this->request->post['months'] = $months;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /admin/users/' . $personId));
    $expectedFlashMessage = "The number of months access must be 0, 1, 12, or 120.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testUpdateEndDateWhenAdminValidDataNotSubscribed() {
    $premiumListId = 2;
    $personId = 100;
    $months = 12;
    $person = [
      'person_id' => $personId
    ];
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->personRepository->shouldReceive('getPersonByIdForAdmin')
      ->once()
      ->with($personId)
      ->andReturn($person);
    $this->personRepository->shouldReceive('getSubscriber')
      ->once()
      ->with($premiumListId, $personId)
      ->andReturn(NULL);
    $this->personRepository->shouldReceive('insertSubscriber')
      ->once()
      ->with($premiumListId, $personId);
    $this->personRepository->shouldReceive('updatePremiumEndDate')
      ->once()
      ->andReturn(1);
    $this->request->post['person_id'] = $personId;
    $this->request->post['months'] = $months;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /admin/users/' . $personId));
    $expectedFlashMessage = "Access has been granted.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testUpdateEndDateWhenAdminValidDataSubscribed() {
    $premiumListId = 2;
    $personId = 100;
    $unsubscribedStatus = 2;
    $subscribedStatus = 1;
    $months = 12;
    $person = [
      'person_id' => $personId
    ];
    $subscriber = [
      'person_id' => $personId,
      'subscriber_status_id' => $unsubscribedStatus
    ];
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->personRepository->shouldReceive('getPersonByIdForAdmin')
      ->once()
      ->with($personId)
      ->andReturn($person);
    $this->personRepository->shouldReceive('getSubscriber')
      ->once()
      ->with($premiumListId, $personId)
      ->andReturn($subscriber);
    $this->personRepository->shouldReceive('updateSubscriber')
      ->once()
      ->with($premiumListId, $personId, $subscribedStatus);
    $this->personRepository->shouldReceive('updatePremiumEndDate')
      ->once()
      ->andReturn(1);
    $this->request->post['person_id'] = $personId;
    $this->request->post['months'] = $months;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /admin/users/' . $personId));
    $expectedFlashMessage = "Access has been granted.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenAdminRevokingAccess() {
    $premiumListId = 2;
    $personId = 100;
    $unsubscribedStatus = 2;
    $months = 0;
    $person = [
      'person_id' => $personId
    ];
    session_start();
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->personRepository->shouldReceive('getPersonByIdForAdmin')
      ->once()
      ->with($personId)
      ->andReturn($person);
    $this->personRepository->shouldReceive('updateSubscriber')
      ->once()
      ->with($premiumListId, $personId, $unsubscribedStatus)
      ->andReturn($person);
    $this->personRepository->shouldReceive('updatePremiumEndDate')
      ->once()
      ->andReturn(1);
    $this->request->post['person_id'] = $personId;
    $this->request->post['months'] = $months;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /admin/users/' . $personId));
    $expectedFlashMessage = "Access has been revoked.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }
}
?>
