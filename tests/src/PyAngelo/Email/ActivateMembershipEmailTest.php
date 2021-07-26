<?php
namespace Tests\src\PyAngelo\Email;

use Mockery;
use PHPUnit\Framework\TestCase;
use PyAngelo\Email\EmailTemplate;
use PyAngelo\Email\ActivateMembershipEmail;

class ActivateMembershipEmailTest extends TestCase {
  public function tearDown(): void {
    Mockery::close();
  }

  public function testActivateMembershipEmail() {
    $dotenv = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../../../');
    $dotenv->load();
    $webDeveloperEmail = $_ENV['WEB_DEVELOPER_EMAIL'];
    $emailTemplate = Mockery::mock('PyAngelo\Email\EmailTemplate');
    $emailTemplate->shouldReceive('addEmailHeader')
      ->twice()
      ->with('Activate Your Free PyAngelo Membership')
      ->andReturn();
    $emailTemplate->shouldReceive('addEmailBodyStart')->twice()->with();
    $emailTemplate->shouldReceive('addEmailParagraph')->times(8);
    $emailTemplate->shouldReceive('addEmailButton')->twice();
    $emailTemplate->shouldReceive('addEmailBodyEnd')->twice()->with();
    $emailTemplate->shouldReceive('addEmailFooterMessage')
      ->twice()
      ->with('This email was generated from the PyAngelo website.');
    $mailRepository = Mockery::mock('PyAngelo\Repositories\MailRepository');
    $mailRepository->shouldReceive('insertTransactionalMail')->once();
    $mailer = Mockery::mock('Framework\Mail\LoggerMail');
    $mailer->shouldReceive('send')->once();

    $activateMembershipEmail = new ActivateMembershipEmail(
      $emailTemplate,
      $mailRepository,
      $mailer,
      $webDeveloperEmail
    );

    $activateToken = 'a-test-reset-token';
    $firstName = 'Fast';
    $surname = 'Freddy';
    $toEmail = 'fast@hotmail.com';
    $mailInfo = [
      'requestScheme' => 'https',
      'serverName' => 'www.pyangelo.com',
      'token' => $activateToken,
      'givenName' => $firstName,
      'familyName' => $surname,
      'toEmail' => $toEmail
    ];
    $mailQueueId = $activateMembershipEmail->queueEmail($mailInfo);
    $activateMembershipEmail->sendEmail($mailInfo);
    $this->assertTrue(true);
  }
}
