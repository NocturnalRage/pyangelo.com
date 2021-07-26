<?php
namespace tests\src\PyAngelo\Email;

use Mockery;
use PHPUnit\Framework\TestCase;
use PyAngelo\Email\EmailTemplate;
use PyAngelo\Email\WhyCancelEmail;

class WhyCancelEmailTest extends TestCase {
  public function tearDown(): void {
    Mockery::close();
  }

  public function testWhyCancelEmail() {
    $dotenv = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../../../');
    $dotenv->load();
    $webDeveloperEmail = $_ENV['WEB_DEVELOPER_EMAIL'];
    $emailTemplate = Mockery::mock('PyAngelo\Email\EmailTemplate');
    $emailTemplate->shouldReceive('addEmailHeader')
      ->twice()
      ->with("I'd Appreciate Your Feedback")
      ->andReturn();
    $emailTemplate->shouldReceive('addEmailBodyStart')->twice()->with();
    $emailTemplate->shouldReceive('addEmailParagraph')->times(4);
    $emailTemplate->shouldReceive('addEmailBodyEnd')->twice()->with();
    $emailTemplate->shouldReceive('addEmailFooterMessage')
      ->twice()
      ->with('Thanks for being a premium member and supporting PyAngelo');
    $mailRepository = Mockery::mock('PyAngelo\Repositories\MailRepository');
    $mailRepository->shouldReceive('insertTransactionalMail')->once();
    $mailer = Mockery::mock('Framework\Mail\LoggerMail');
    $mailer->shouldReceive('send')->once();

    $email= new WhyCancelEmail(
      $emailTemplate,
      $mailRepository,
      $mailer,
      $webDeveloperEmail
    );

    $firstName = 'Fast';
    $surname = 'Freddy';
    $toEmail = 'fast@hotmail.com';
    $mailInfo = [
      'givenName' => $firstName,
      'toEmail' => $toEmail
    ];
    $mailQueueId = $email->queueEmail($mailInfo);
    $email->sendEmail($mailInfo);
    $this->assertTrue(true);
  }
}
