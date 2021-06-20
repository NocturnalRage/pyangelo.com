<?php
namespace Tests\src\PyAngelo\FormServices;

use Mockery;
use PHPUnit\Framework\TestCase;
use PyAngelo\FormServices\RegisterFormService;

class RegisterFormServiceTest extends TestCase {
  public function setUp(): void {
    $this->personRepository = Mockery::mock('PyAngelo\Repositories\PersonRepository');
    $this->activateMembershipEmail = Mockery::mock('PyAngelo\Email\ActivateMembershipEmail');
    $this->countryDetector = Mockery::mock('PyAngelo\Utilities\CountryDetector');
    $this->registerFormService = new RegisterFormService(
      $this->personRepository,
      $this->activateMembershipEmail,
      $this->countryDetector,
      [ 'requestScheme' => 'https', 'serverName' => 'www.pyangelo.com' ]
    );
  }

  public function tearDown(): void {
    Mockery::close();
  }

  public function testCreatePersonWithNoFormData() {
    $formData = [];

    $success = $this->registerFormService->createPerson($formData);
    $flashMessage = $this->registerFormService->getFlashMessage();
    $errors = $this->registerFormService->getErrors();
    $expectedFlashMessage = 'There were some errors. Please fix these and then we will create your free account.';
    $expectedErrors = [
      'givenName' => 'The given name field cannot be blank.',
      'familyName' => 'The family name field cannot be blank.',
      'email' => 'You must supply an email address.',
      'loginPassword' => 'You must choose a password.',
      'consent' => 'You must agree to the terms of use and privacy policy to create your account.'
    ];
    $this->assertFalse($success);
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testCreatePersonWithDataTooLong() {
    $formData = [
      'givenName' => 'A really really long given name that is more than 100 characters in length is going to be rejected by the validation',
      'familyName' => 'A really really long family name that is more than 100 characters in length is going to be rejected by the validation',
      'email' => 'fred@areallyreallyreallyreallyreallyreallyreallylongdomainnamethatmakestheentireemaillongerthan100charactersinlength.com.au',
      'loginPassword' => 'A Password That is Greater Than 30 Characters in Length',
      'consent' => 'You must agree to the terms of use and privacy policy to create your account.'
    ];

    $success = $this->registerFormService->createPerson($formData);
    $flashMessage = $this->registerFormService->getFlashMessage();
    $errors = $this->registerFormService->getErrors();
    $expectedFlashMessage = 'There were some errors. Please fix these and then we will create your free account.';
    $expectedErrors = [
      'givenName' => 'The given name can be no longer than 100 characters.',
      'familyName' => 'The family name can be no longer than 100 characters.',
      'email' => 'The email address can be no longer than 100 characters.',
      'loginPassword' => 'The password must be between 4 and 30 characters in length.'

    ];
    $this->assertFalse($success);
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testCreatePersonWithInvalidEmailAndPasswordTooShort() {
    $formData = [
      'givenName' => 'Fred',
      'familyName' => 'Fast',
      'email' => 'fred@invalid',
      'loginPassword' => 'abc',
      'consent' => 1
    ];

    $success = $this->registerFormService->createPerson($formData);
    $flashMessage = $this->registerFormService->getFlashMessage();
    $errors = $this->registerFormService->getErrors();
    $expectedFlashMessage = 'There were some errors. Please fix these and then we will create your free account.';
    $expectedErrors = [
      'email' => 'The email address is not valid.',
      'loginPassword' => 'The password must be between 4 and 30 characters in length.'

    ];
    $this->assertFalse($success);
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testCreatePersonWithExistingActiveUser() {
    $givenName = 'Fred';
    $familyName = 'Fast';
    $email = 'fred@fastcubing.com';
    $loginPassword = 'secret';
    $person = [
      'person_id' => 999,
      'active' => 1
    ];
    $this->personRepository->shouldReceive('getPersonActiveOrNotByEmail')
      ->once()
      ->with($email)
      ->andReturn($person);
    $formData = [
      'givenName' => $givenName,
      'familyName' => $familyName,
      'email' => $email,
      'loginPassword' => $loginPassword,
      'consent' => 1
    ];

    $success = $this->registerFormService->createPerson($formData);
    $flashMessage = $this->registerFormService->getFlashMessage();
    $errors = $this->registerFormService->getErrors();
    $expectedFlashMessage = 'There were some errors. Please fix these and then we will create your free account.';
    $expectedErrors = [
      'email' => 'This email address is already taken. Please enter another email address.'
    ];
    $this->assertFalse($success);
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testCreatePersonWithExistingInactiveUserNotBounced() {
    $givenName = 'Fred';
    $familyName = 'Fast';
    $email = 'fred@fastcubing.com';
    $loginPassword = 'secret';
    $countryCode = 'AU';
    $emailStatusId = 1;
    $personId = 999;
    $person = [
      'person_id' => $personId,
      'active' => 0,
      'email_status_id' => $emailStatusId
    ];
    $this->personRepository->shouldReceive('getPersonActiveOrNotByEmail')
      ->times(2)
      ->with($email)
      ->andReturn($person);
    $this->personRepository->shouldReceive('updatePerson')
      ->once()
      ->with($personId, $givenName, $familyName, $email, 0, $countryCode, $countryCode)
      ->andReturn(1);
    $this->personRepository->shouldReceive('updatePassword')
      ->once()
      ->with($personId, $loginPassword)
      ->andReturn(1);
    $this->personRepository->shouldReceive('insertMembershipActivate')->once();
    $activateMembershipEmail = Mockery::mock('PyAngelo\Email\ActivateMembershipEmail');
    $this->activateMembershipEmail->shouldReceive('sendEmail')->once();
    $this->countryDetector->shouldReceive('getCountryFromIp')
      ->once()
      ->with()
      ->andReturn('AU');
    $formData = [
      'givenName' => $givenName,
      'familyName' => $familyName,
      'email' => $email,
      'loginPassword' => $loginPassword,
      'consent' => 1
    ];

    $success = $this->registerFormService->createPerson($formData);
    $flashMessage = $this->registerFormService->getFlashMessage();
    $errors = $this->registerFormService->getErrors();
    $this->assertTrue($success);
    $this->assertNull($flashMessage);
    $this->assertEmpty($errors);
  }

  public function testCreatePersonWithExistingInactiveUserBounced() {
    $givenName = 'Fred';
    $familyName = 'Fast';
    $email = 'fred@fastcubing.com';
    $loginPassword = 'secret';
    $countryCode = 'AU';
    $emailStatusId = 2;
    $personId = 999;
    $person = [
      'person_id' => $personId,
      'active' => 0,
      'email_status_id' => $emailStatusId
    ];
    $this->personRepository->shouldReceive('getPersonActiveOrNotByEmail')
      ->once()
      ->with($email)
      ->andReturn($person);
    $formData = [
      'givenName' => $givenName,
      'familyName' => $familyName,
      'email' => $email,
      'loginPassword' => $loginPassword,
      'consent' => 1
    ];

    $success = $this->registerFormService->createPerson($formData);
    $flashMessage = $this->registerFormService->getFlashMessage();
    $errors = $this->registerFormService->getErrors();
    $this->assertFalse($success);
    $expectedFlashMessage = 'There were some errors. Please fix these and then we will create your free account.';
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $expectedErrors = [
      'email' => "We've already sent an activation email to this address and it bounced. This means there is something wrong with this email account. I suggest trying a different email address."
    ];
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testCreatePersonWithNoExistingUser() {
    $givenName = 'Fred';
    $familyName = 'Fast';
    $email = 'fred@fastcubing.com';
    $loginPassword = 'secret';
    $countryCode = 'AU';
    $personId = 999;
    $this->personRepository->shouldReceive('getPersonActiveOrNotByEmail')
      ->once()
      ->with($email)
      ->andReturn(NULL);
    $this->personRepository->shouldReceive('insertFreeMember')
      ->once()
      ->with($givenName, $familyName, $email, $loginPassword, $countryCode, $countryCode)
      ->andReturn($personId);
    $this->personRepository->shouldReceive('insertMembershipActivate')->once();
    $this->activateMembershipEmail->shouldReceive('sendEmail')->once();
    $this->countryDetector->shouldReceive('getCountryFromIp')
      ->once()
      ->with()
      ->andReturn('AU');
    $formData = [
      'givenName' => $givenName,
      'familyName' => $familyName,
      'email' => $email,
      'loginPassword' => $loginPassword,
      'consent' => 1
    ];

    $success = $this->registerFormService->createPerson($formData);
    $flashMessage = $this->registerFormService->getFlashMessage();
    $errors = $this->registerFormService->getErrors();
    $this->assertTrue($success);
    $this->assertNull($flashMessage);
    $this->assertEmpty($errors);
  }
}
