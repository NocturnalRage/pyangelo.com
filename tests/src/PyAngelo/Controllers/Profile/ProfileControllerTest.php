<?php
namespace Tests\src\PyAngelo\Controllers\Profile;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Profile\ProfileController;

class ProfileControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->personRepository = Mockery::mock('PyAngelo\Repositories\PersonRepository');
    $this->avatar = Mockery::mock('Framework\Contracts\AvatarContract');
    $this->controller = new ProfileController (
      $this->request,
      $this->response,
      $this->auth,
      $this->personRepository,
      $this->avatar
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Profile\ProfileController');
  }

  public function testRedirectToLoginPageWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $expectedFlashMessage = "You must be logged in to view your profile.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  public function testProfileControllerWhenLoggedIn() {
    $personId = 99;
    $person = [
      'person_id' => $personId,
      'given_name' => 'Fred',
      'family_name' => 'Fearless',
      'email' => 'Fearless',
      'created_at' => '2016-01-01 10:00:00',
    ];
    $points = ['points' => 100];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();

    $this->personRepository->shouldReceive('getPoints')->once()->with($personId)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/profile.html.php';
    $expectedPageTitle = 'Profile of ' . $person['given_name'] . ' ' . $person['family_name'];
    $expectedMetaDescription = "Your PyAngelo profile.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
