<?php
namespace Tests\src\PyAngelo\FormServices;

use Mockery;
use PHPUnit\Framework\TestCase;
use PyAngelo\FormServices\ForgotPasswordFormService;

class ForgotPasswordFormServiceTest extends TestCase {
  public function setUp(): void {
    $this->personRepository = Mockery::mock('PyAngelo\Repositories\PersonRepository');
    $this->forgotPasswordEmail = Mockery::mock('PyAngelo\Email\forgotPasswordEmail');
    $this->forgotPasswordFormService = new ForgotPasswordFormService(
      $this->personRepository,
      $this->forgotPasswordEmail,
      [ 'requestScheme' => 'https', 'serverName' => 'www.pyangelo.com' ]
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testSaveAndSendWithNoFormData() {
    $formData = [];

    $success = $this->forgotPasswordFormService->saveRequestAndSendEmail($formData);
    $flashMessage = $this->forgotPasswordFormService->getFlashMessage();
    $errors = $this->forgotPasswordFormService->getErrors();
    $expectedFlashMessage = 'The email was not a valid address. Please check it and submit the form again so we can send you a password reset link.';
    $expectedErrors = [
      'email' => 'You must enter the email you used to create your account in order to reset your password.'
    ];
    $this->assertFalse($success);
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testSaveAndSendWithInvalidEmail() {
    $formData = ['email' => 'fred'];

    $success = $this->forgotPasswordFormService->saveRequestAndSendEmail($formData);
    $flashMessage = $this->forgotPasswordFormService->getFlashMessage();
    $errors = $this->forgotPasswordFormService->getErrors();
    $expectedFlashMessage = 'The email was not a valid address. Please check it and submit the form again so we can send you a password reset link.';
    $expectedErrors = [
      'email' => 'The email you entered is not a valid addresss.'
    ];
    $this->assertFalse($success);
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  /* We want this functionality
   * as people should not be able to tell if an email
   * is in the database by trying to reset the password
   */
  public function testSaveAndSendWithEmailThatDoesNotExist() {
    $email = 'fred@fastcubing.com';
    $this->personRepository->shouldReceive('getPersonByEmail')
      ->once()
      ->with($email)
      ->andReturn(NULL);
    $formData = ['email' => $email];

    $success = $this->forgotPasswordFormService->saveRequestAndSendEmail($formData);
    $this->assertTrue($success);
    $this->assertEmpty($this->forgotPasswordFormService->getErrors());
  }

  public function testSaveAndSendWithEmailThatDoesExist() {
    $token = 'reset-token';
    $email = 'fred@fastcubing.com';
    $givenName = 'Fred';
    $person = [
      'person_id' => 99,
      'given_name' => $givenName,
      'email' => $email
    ];
    $this->personRepository->shouldReceive('getPersonByEmail')
      ->once()
      ->with($email)
      ->andReturn($person);
    $this->personRepository->shouldReceive('insertPasswordResetRequest')
      ->once()
      ->with($person['person_id'], Mockery::any());
    $this->forgotPasswordEmail->shouldReceive('sendEmail')->once();
    $formData = ['email' => $email];

    $success = $this->forgotPasswordFormService->saveRequestAndSendEmail($formData);
    $this->assertTrue($success);
    $this->assertEmpty($this->forgotPasswordFormService->getErrors());
  }
}
