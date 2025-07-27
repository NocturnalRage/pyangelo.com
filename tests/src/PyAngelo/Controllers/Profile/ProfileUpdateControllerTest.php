<?php
namespace tests\src\PyAngelo\Controllers\Profile;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Profile\ProfileUpdateController;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class ProfileUpdateControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $personRepository;
  protected $countryRepository;
  protected $countryDetector;
  protected $stripeWrapper;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->personRepository = Mockery::mock('PyAngelo\Repositories\PersonRepository');
    $this->countryRepository = Mockery::mock('PyAngelo\Repositories\CountryRepository');
    $this->countryDetector = Mockery::mock('PyAngelo\Utilities\CountryDetector');
    $this->stripeWrapper = Mockery::mock('Framework\Billing\StripeWrapper');
    $this->controller = new ProfileUpdateController(
      $this->request,
      $this->response,
      $this->auth,
      $this->personRepository,
      $this->countryRepository,
      $this->countryDetector,
      $this->stripeWrapper
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Profile\ProfileUpdateController');
  }

  #[RunInSeparateProcess]
  public function testUpdateProfileWhenNotLoggedIn() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $expectedFlashMessage = "You must be logged in to edit your profile.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  #[RunInSeparateProcess]
  public function testUpdateProfileWhenLoggedInWithInvalidCrsfToken() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /profile'));
    $expectedFlashMessage = "Please update your profile from the PyAngelo website.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  #[RunInSeparateProcess]
  public function testUpdateProfileWithNoFormData() {
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /profile/edit'));
    $expectedFlashMessage = "There were some errors. Please fix these and then we can update your profile.";
    $expectedErrors = [
      'given_name' => 'The given name field cannot be blank.',
      'family_name' => 'The family name field cannot be blank.',
      'email' => 'You must supply an email address.',
      'country_code' => 'You must select the country you are from.'
    ];
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
    $this->assertSame($expectedErrors, $_SESSION['errors']);
  }

  #[RunInSeparateProcess]
  public function testUpdateProfileWithDataTooLongAndInvalidEmail() {
    $countryCode = 'FK';
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->countryRepository->shouldReceive('getCountry')
      ->once()
      ->with($countryCode)
      ->andReturn(NULL);
    $this->request->post['given_name'] = 'A really really long name that is more than 100 characters long and seems to be hard to type into a small field';
    $this->request->post['family_name'] = 'A really really long name that is more than 100 characters long and seems to be hard to type into a small field';
    $this->request->post['email'] = 'fred@hotmail';
    $this->request->post['country_code'] = $countryCode;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /profile/edit'));
    $expectedFlashMessage = "There were some errors. Please fix these and then we can update your profile.";
    $expectedErrors = [
      'given_name' => 'The given name can be no longer than 100 characters.',
      'family_name' => 'The family name can be no longer than 100 characters.',
      'email' => 'The email address is not valid.',
      'country_code' => 'You must select a valid country from the list.'
    ];
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
    $this->assertSame($expectedErrors, $_SESSION['errors']);
  }

  #[RunInSeparateProcess]
  public function testUpdateProfileWithExistingEmail() {
    $countryCode = 'AU';
    $country = ['country_code' => 'AU', 'country_name' => 'Australia'];
    $personId = 999;
    $otherPersonId = 888;
    $givenName = 'Fred';
    $familyName = 'Fearless';
    $email = 'fastfred@hotmail.com';
    $person = [
      'person_id' => $otherPersonId,
      'given_name' => $givenName,
      'family_name' => $familyName,
      'email' => $email
    ];
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->personRepository->shouldReceive('getPersonActiveOrNotByEmail')
      ->once()
      ->with($email)
      ->andReturn($person);
    $this->countryRepository->shouldReceive('getCountry')
      ->once()
      ->with($countryCode)
      ->andReturn($country);
    $this->request->post['given_name'] = $givenName;
    $this->request->post['family_name'] = $familyName;
    $this->request->post['email'] = $email;
    $this->request->post['country_code'] = $countryCode;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /profile/edit'));
    $expectedFlashMessage = "There were some errors. Please fix these and then we can update your profile.";
    $expectedErrors = [
      'email' => 'This email address is already in use.'
    ];
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
    $this->assertSame($expectedErrors, $_SESSION['errors']);
  }

  #[RunInSeparateProcess]
  public function testUpdateProfileWithoutUpdatingEmail() {
    $countryCode = 'AU';
    $country = ['country_code' => 'AU', 'country_name' => 'Australia'];
    $personId = 999;
    $givenName = 'Fred';
    $familyName = 'Fearless';
    $email = 'fastfred@hotmail.com';
    $person = [
      'person_id' => $personId,
      'given_name' => $givenName,
      'family_name' => $familyName,
      'email' => $email,
      'stripe_customer_id' => ''
    ];
    $countryCode = 'AU';
    $detectedCountryCode = 'AU';
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->personRepository->shouldReceive('getPersonActiveOrNotByEmail')
      ->once()
      ->with($email)
      ->andReturn($person);
    $this->personRepository->shouldReceive('updatePerson')
      ->once()
      ->with($personId, $givenName, $familyName, $email, 1, $countryCode, $detectedCountryCode)
      ->andReturn(1);
    $this->countryRepository->shouldReceive('getCountry')
      ->once()
      ->with($countryCode)
      ->andReturn($country);
    $this->countryDetector->shouldReceive('getCountryFromIp')
      ->once()
      ->with()
      ->andReturn($detectedCountryCode);
    $this->request->post['given_name'] = $givenName;
    $this->request->post['family_name'] = $familyName;
    $this->request->post['email'] = $email;
    $this->request->post['country_code'] = $countryCode;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /profile'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = "Your profile has been updated.";
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  #[RunInSeparateProcess]
  public function testUpdateProfileWithoutUpdatingEmailAndStripeCustomer() {
    $countryCode = 'AU';
    $country = ['country_code' => 'AU', 'country_name' => 'Australia'];
    $personId = 999;
    $givenName = 'Fred';
    $familyName = 'Fearless';
    $email = 'fastfred@hotmail.com';
    $person = [
      'person_id' => $personId,
      'given_name' => $givenName,
      'family_name' => $familyName,
      'email' => $email,
      'stripe_customer_id' => 'CUS_000'
    ];
    $countryCode = 'AU';
    $detectedCountryCode = 'AU';
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->twice()->with()->andReturn($personId);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->personRepository->shouldReceive('getPersonActiveOrNotByEmail')
      ->once()
      ->with($email)
      ->andReturn($person);
    $this->personRepository->shouldReceive('updatePerson')
      ->once()
      ->with($personId, $givenName, $familyName, $email, 1, $countryCode, $detectedCountryCode)
      ->andReturn(1);
    $this->countryRepository->shouldReceive('getCountry')
      ->once()
      ->with($countryCode)
      ->andReturn($country);
    $this->countryDetector->shouldReceive('getCountryFromIp')
      ->once()
      ->with()
      ->andReturn($detectedCountryCode);
    $this->request->post['given_name'] = $givenName;
    $this->request->post['family_name'] = $familyName;
    $this->request->post['email'] = $email;
    $this->request->post['country_code'] = $countryCode;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /profile'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $expectedFlashMessage = "Your profile has been updated.";
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }

  #[RunInSeparateProcess]
  public function testUpdateProfileWithValidData() {
    $countryCode = 'AU';
    $country = ['country_code' => 'AU', 'country_name' => 'Australia'];
    $personId = 99;
    $givenName = 'Fred';
    $familyName = 'Fearless';
    $oldEmail = 'fast@gmail.com';
    $stripeCustomerId = 'CUS_000';
    $person = [
      'person_id' => $personId,
      'given_name' => $givenName,
      'family_name' => $familyName,
      'email' => $oldEmail,
      'stripe_customer_id' => $stripeCustomerId
    ];
    $newEmail = 'fastfred@hotmail.com';
    $countryCode = 'AU';
    $detectedCountryCode = 'AU';
    session_start();
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('person')->once()->with()->andReturn($person);
    $this->personRepository->shouldReceive('getPersonActiveOrNotByEmail')
      ->once()
      ->with($newEmail)
      ->andReturn(NULL);
    $this->personRepository->shouldReceive('updatePerson')
      ->once()
      ->with($personId, $givenName, $familyName, $newEmail, 1, $countryCode, $detectedCountryCode)
      ->andReturn(1);
    $this->countryRepository->shouldReceive('getCountry')
      ->once()
      ->with($countryCode)
      ->andReturn($country);
    $this->countryDetector->shouldReceive('getCountryFromIp')
      ->once()
      ->with()
      ->andReturn($detectedCountryCode);
    $this->stripeWrapper->shouldReceive('updateEmail')
      ->once()
      ->with($stripeCustomerId, $newEmail)
      ->andReturn($detectedCountryCode);
    $this->request->post['given_name'] = $givenName;
    $this->request->post['family_name'] = $familyName;
    $this->request->post['email'] = $newEmail;
    $this->request->post['country_code'] = $countryCode;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /profile'));
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($newEmail, $_SESSION['loginEmail']);
    $expectedFlashMessage = "Your profile has been updated.";
    $this->assertSame($expectedFlashMessage, $_SESSION['flash']['message']);
  }
}
?>
