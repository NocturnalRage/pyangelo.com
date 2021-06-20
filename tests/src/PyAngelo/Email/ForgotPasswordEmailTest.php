<?php
namespace Tests\src\PyAngelo\Email;

use Mockery;
use PHPUnit\Framework\TestCase;
use PyAngelo\Email\EmailTemplate;
use PyAngelo\Email\ForgotPasswordEmail;

class ForgotPasswordEmailTest extends TestCase {
  public function tearDown(): void {
    Mockery::close();
  }

  public function testForgotPasswordEmail() {
    $emailTemplate = Mockery::mock('PyAngelo\Email\EmailTemplate');
    $emailTemplate->shouldReceive('addEmailHeader')
      ->twice()
      ->with('Password Reset Request | PyAngelo')
      ->andReturn();
    $emailTemplate->shouldReceive('addEmailBodyStart')->twice()->with();
    $emailTemplate->shouldReceive('addEmailParagraph')->times(10);
    $emailTemplate->shouldReceive('addEmailButton')->twice();
    $emailTemplate->shouldReceive('addEmailBodyEnd')->twice()->with();
    $emailTemplate->shouldReceive('addEmailFooterMessage')
      ->twice()
      ->with('This email was generated from the PyAngelo website');
    $mailRepository = Mockery::mock('PyAngelo\Repositories\MailRepository');
    $mailRepository->shouldReceive('insertTransactionalMail')->once();
    $mailer = Mockery::mock('Framework\Mail\LoggerMail');
    $mailer->shouldReceive('send')->once();

    $email = new ForgotPasswordEmail(
      $emailTemplate,
      $mailRepository,
      $mailer
    );

    $resetToken = 'a-test-reset-token';
    $firstName = 'Fast';
    $surname = 'Freddy';
    $toEmail = 'fast@hotmail.com';
    $mailInfo = [
      'requestScheme' => 'https',
      'serverName' => 'www.pyangelo.com',
      'token' => $resetToken,
      'givenName' => $firstName,
      'familyName' => $surname,
      'toEmail' => $toEmail
    ];
    $mailQueueId = $email->queueEmail($mailInfo);
    $email->sendEmail($mailInfo);
    $this->assertTrue(true);
  }
}
