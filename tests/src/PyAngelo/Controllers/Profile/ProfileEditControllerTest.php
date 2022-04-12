<?php
namespace tests\src\PyAngelo\Controllers\Profile;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Profile\ProfileEditController;

class ProfileEditControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->countryRepository = Mockery::mock('PyAngelo\Repositories\CountryRepository');
    $this->avatar = Mockery::mock('Framework\Contracts\AvatarContract');
    $this->controller = new ProfileEditController(
      $this->request,
      $this->response,
      $this->auth,
      $this->countryRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Profile\ProfileEditController');
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenNotLoggedIn() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $expectedFlashMessage = "You must be logged in to edit your profile.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testWhenLoggedIn() {
    $countries = [
      [
        'country_code' => 'AU',
        'country_name' => 'Australia'
      ],
      [
        'country_code' => 'NZ',
        'country_name' => 'New Zealand'
      ],
    ];
    $person = [
      'person_id' => 99
    ];
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->countryRepository->shouldReceive('getRealCountries')->once()->with()->andReturn($countries);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/edit.html.php';
    $expectedPageTitle = 'Edit Profile';
    $expectedMetaDescription = "Edit your PyAngelo profile.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
