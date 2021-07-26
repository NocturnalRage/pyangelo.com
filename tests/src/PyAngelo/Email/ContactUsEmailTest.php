<?php
namespace Tests\src\PyAngelo\Email;

use Mockery;
use PHPUnit\Framework\TestCase;
use PyAngelo\Email\EmailTemplate;
use PyAngelo\Email\ContactUsEmail;

class ContactUsEmailTest extends TestCase {
  public function tearDown(): void {
    Mockery::close();
  }

  public function testContactUsEmail() {
    $dotenv = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../../../');
    $dotenv->load();
    $webDeveloperEmail = $_ENV['WEB_DEVELOPER_EMAIL'];
    $emailTemplate = Mockery::mock('PyAngelo\Email\EmailTemplate');
    $emailTemplate->shouldReceive('addEmailHeader')
      ->twice()
      ->with('Contact Us | PyAngelo')
      ->andReturn();
    $emailTemplate->shouldReceive('addEmailBodyStart')->twice()->with();
    $emailTemplate->shouldReceive('addEmailParagraph')->times(2);
    $emailTemplate->shouldReceive('addEmailBodyEnd')->twice()->with();
    $emailTemplate->shouldReceive('addEmailFooterMessage')
      ->twice()
      ->with('This email was generated from the contact page on the PyAngelo website.');
    $mailRepository = Mockery::mock('PyAngelo\Repositories\MailRepository');
    $mailRepository->shouldReceive('insertTransactionalMail')->once();
    $mailer = Mockery::mock('Framework\Mail\LoggerMail');
    $mailer->shouldReceive('send')->once();

    $email = new ContactUsEmail(
      $emailTemplate,
      $mailRepository,
      $mailer,
      $webDeveloperEmail
    );

    $replyEmail = 'fast@hotmail.com';
    $name = 'Fast Freddy';
    $inquiry = 'What is happening?';
    $mailInfo = [
      'replyEmail' => $replyEmail,
      'name' => $name,
      'inquiry' => $inquiry
    ];
    $mailQueueId = $email->queueEmail($mailInfo);
    $email->sendEmail($mailInfo);
    $this->assertTrue(true);
  }
}
