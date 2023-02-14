<?php
namespace tests\src\PyAngelo\Email;

use Mockery;
use PHPUnit\Framework\TestCase;
use PyAngelo\Email\EmailTemplate;
use PyAngelo\Email\StripeWebhookEmails;

class StripeWebhookEmailsTest extends TestCase {
  protected $emailTemplate;
  protected $mailRepository;
  protected $mailer;
  protected $webDeveloperEmail;
  protected $stripeWebhookEmails;

  public function setUp(): void {
    $this->emailTemplate = Mockery::mock('PyAngelo\Email\EmailTemplate');
    $this->mailRepository = Mockery::mock('PyAngelo\Repositories\MailRepository');
    $this->mailer = Mockery::mock('Framework\Contracts\MailContract');
    $dotenv = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../../../');
    $dotenv->load();
    $this->webDeveloperEmail = $_ENV['WEB_DEVELOPER_EMAIL'];

    $this->stripeWebhookEmails = new StripeWebhookEmails (
      $this->emailTemplate,
      $this->mailRepository,
      $this->mailer,
      $this->webDeveloperEmail
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->stripeWebhookEmails), 'PyAngelo\Email\StripeWebhookEmails');
  }

  public function testDefaultEmailAddresses() {
    $this->assertSame($this->stripeWebhookEmails->getFromEmail(), $this->webDeveloperEmail);
    $this->assertSame($this->stripeWebhookEmails->getReplyEmail(), $this->webDeveloperEmail);
  }

  public function testInvalidSignatureEmail() {
    $this->emailTemplate->shouldReceive('addEmailHeader')
      ->twice()
      ->with('Stripe Webhook Called With Invalid Signature')
      ->andReturn();
    $this->emailTemplate->shouldReceive('addEmailBodyStart')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailParagraph')->twice();
    $this->emailTemplate->shouldReceive('addEmailBodyEnd')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailFooterMessage')
      ->twice()
      ->with('This email was generated from the PyAngelo Stripe Webhook.');
    $this->mailRepository->shouldReceive('insertTransactionalMail')->once();
    $this->mailer->shouldReceive('send')->once();

    $mailInfo = [
      'emailType' => 'invalidSignature'
    ];
    $mailQueueId = $this->stripeWebhookEmails->queueEmail($mailInfo);
    $this->stripeWebhookEmails->sendEmail($mailInfo);
    $this->assertTrue(true);
  }

  public function testInvalidPayloadEmail() {
    $this->emailTemplate->shouldReceive('addEmailHeader')
      ->twice()
      ->with('Stripe Webhook Called With Invalid Payload')
      ->andReturn();
    $this->emailTemplate->shouldReceive('addEmailBodyStart')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailParagraph')->twice();
    $this->emailTemplate->shouldReceive('addEmailBodyEnd')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailFooterMessage')
      ->twice()
      ->with('This email was generated from the PyAngelo Stripe Webhook.');
    $this->mailRepository->shouldReceive('insertTransactionalMail')->once();
    $this->mailer->shouldReceive('send')->once();

    $mailInfo = [
      'emailType' => 'invalidPayload'
    ];
    $mailQueueId = $this->stripeWebhookEmails->queueEmail($mailInfo);
    $this->stripeWebhookEmails->sendEmail($mailInfo);
    $this->assertTrue(true);
  }

  public function testNoStripeEventEmail() {
    $this->emailTemplate->shouldReceive('addEmailHeader')
      ->twice()
      ->with('Stripe Webhook Called But Event Was Not from Stripe')
      ->andReturn();
    $this->emailTemplate->shouldReceive('addEmailBodyStart')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailParagraph')->twice();
    $this->emailTemplate->shouldReceive('addEmailBodyEnd')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailFooterMessage')
      ->twice()
      ->with('This email was generated from the PyAngelo Stripe Webhook.');
    $this->mailRepository->shouldReceive('insertTransactionalMail')->once();
    $this->mailer->shouldReceive('send')->once();

    $mailInfo = [
      'emailType' => 'noStripeEvent',
      'stripeEventId' => 'EV_00000000'
    ];
    $mailQueueId = $this->stripeWebhookEmails->queueEmail($mailInfo);
    $this->stripeWebhookEmails->sendEmail($mailInfo);
    $this->assertTrue(true);
  }

  public function testStripeEventAlreadyProcessedEmail() {
    $this->emailTemplate->shouldReceive('addEmailHeader')
      ->twice()
      ->with('Stripe Webhook Sent a Duplicate Event')
      ->andReturn();
    $this->emailTemplate->shouldReceive('addEmailBodyStart')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailParagraph')->twice();
    $this->emailTemplate->shouldReceive('addEmailBodyEnd')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailFooterMessage')
      ->twice()
      ->with('This email was generated from the PyAngelo Stripe Webhook.');
    $this->mailRepository->shouldReceive('insertTransactionalMail')->once();
    $this->mailer->shouldReceive('send')->once();

    $mailInfo = [
      'emailType' => 'stripeEventAlreadyProcessed',
      'stripeEventId' => 'EV_00000000'
    ];
    $mailQueueId = $this->stripeWebhookEmails->queueEmail($mailInfo);
    $this->stripeWebhookEmails->sendEmail($mailInfo);
    $this->assertTrue(true);
  }

  public function testUnhandledWebhookEmail() {
    $this->emailTemplate->shouldReceive('addEmailHeader')
      ->twice()
      ->with('PyAngelo Stripe Webhook Does Not Handle This Event')
      ->andReturn();
    $this->emailTemplate->shouldReceive('addEmailBodyStart')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailParagraph')->twice();
    $this->emailTemplate->shouldReceive('addEmailBodyEnd')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailFooterMessage')
      ->twice()
      ->with('This email was generated from the PyAngelo Stripe Webhook.');
    $this->mailRepository->shouldReceive('insertTransactionalMail')->once();
    $this->mailer->shouldReceive('send')->once();

    $mailInfo = [
      'emailType' => 'unhandledWebhook',
      'stripeEventType' => 'no.such.event'
    ];
    $mailQueueId = $this->stripeWebhookEmails->queueEmail($mailInfo);
    $this->stripeWebhookEmails->sendEmail($mailInfo);
    $this->assertTrue(true);
  }

  public function testNoStripeSubscriptionEmail() {
    $this->emailTemplate->shouldReceive('addEmailHeader')
      ->twice()
      ->with('Stripe Webhook Called But Subscription Does Not Exist')
      ->andReturn();
    $this->emailTemplate->shouldReceive('addEmailBodyStart')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailParagraph')->twice();
    $this->emailTemplate->shouldReceive('addEmailBodyEnd')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailFooterMessage')
      ->twice()
      ->with('This email was generated from the PyAngelo Stripe Webhook.');
    $this->mailRepository->shouldReceive('insertTransactionalMail')->once();
    $this->mailer->shouldReceive('send')->once();

    $mailInfo = [
      'emailType' => 'noStripeSubscription',
      'stripeSubscriptionId' => 'SUB_00000000'
    ];
    $mailQueueId = $this->stripeWebhookEmails->queueEmail($mailInfo);
    $this->stripeWebhookEmails->sendEmail($mailInfo);
    $this->assertTrue(true);
  }

  public function testCouldNotRecordChargeEmail() {
    $this->emailTemplate->shouldReceive('addEmailHeader')
      ->twice()
      ->with('Stripe Webhook Could Not Record Charge')
      ->andReturn();
    $this->emailTemplate->shouldReceive('addEmailBodyStart')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailParagraph')->twice();
    $this->emailTemplate->shouldReceive('addEmailBodyEnd')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailFooterMessage')
      ->twice()
      ->with('This email was generated from the PyAngelo Stripe Webhook.');
    $this->mailRepository->shouldReceive('insertTransactionalMail')->once();
    $this->mailer->shouldReceive('send')->once();

    $mailInfo = [
      'emailType' => 'couldNotRecordCharge',
      'stripeChargeId' => 'CHG_00000000'
    ];
    $mailQueueId = $this->stripeWebhookEmails->queueEmail($mailInfo);
    $this->stripeWebhookEmails->sendEmail($mailInfo);
    $this->assertTrue(true);
  }

  public function testPaymentReceivedEmail() {
    $this->emailTemplate->shouldReceive('addEmailHeader')
      ->twice()
      ->with('PyAngelo Premium Membership Payment')
      ->andReturn();
    $this->emailTemplate->shouldReceive('addEmailBodyStart')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailParagraph')->twice();
    $this->emailTemplate->shouldReceive('addEmailBodyEnd')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailFooterMessage')
      ->twice()
      ->with('We love coding!');
    $this->mailRepository->shouldReceive('insertTransactionalMail')->once();
    $this->mailer->shouldReceive('send')->once();

    $mailInfo = [
      'emailType' => 'paymentReceived',
      'toEmail' => 'fastfred@hotmail.com',
      'givenName' => 'Fred',
      'amountPaidMoney' => '$10.00'
    ];
    $mailQueueId = $this->stripeWebhookEmails->queueEmail($mailInfo);
    $this->stripeWebhookEmails->sendEmail($mailInfo);
    $this->assertTrue(true);
  }

  public function testPaymentFailedEmail() {
    $this->emailTemplate->shouldReceive('addEmailHeader')
      ->twice()
      ->with('PyAngelo Payment Failed')
      ->andReturn();
    $this->emailTemplate->shouldReceive('addEmailBodyStart')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailParagraph')->twice();
    $this->emailTemplate->shouldReceive('addEmailBodyEnd')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailFooterMessage')
      ->twice()
      ->with('We love coding!');
    $this->mailRepository->shouldReceive('insertTransactionalMail')->once();
    $this->mailer->shouldReceive('send')->once();

    $mailInfo = [
      'emailType' => 'paymentFailed',
      'toEmail' => 'fastfred@hotmail.com',
      'givenName' => 'Fred',
      'amountDueMoney' => '$10.00',
      'retryPaymentDays' => 3
    ];
    $mailQueueId = $this->stripeWebhookEmails->queueEmail($mailInfo);
    $this->stripeWebhookEmails->sendEmail($mailInfo);
    $this->assertTrue(true);
  }

  public function testPingWebhookEmail() {
    $this->emailTemplate->shouldReceive('addEmailHeader')
      ->twice()
      ->with('Stripe Sent a Ping Webhook Event')
      ->andReturn();
    $this->emailTemplate->shouldReceive('addEmailBodyStart')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailParagraph')->twice();
    $this->emailTemplate->shouldReceive('addEmailBodyEnd')->twice()->with();
    $this->emailTemplate->shouldReceive('addEmailFooterMessage')
      ->twice()
      ->with('This email was generated from the PyAngelo Stripe Webhook.');
    $this->mailRepository->shouldReceive('insertTransactionalMail')->once();
    $this->mailer->shouldReceive('send')->once();

    $mailInfo = [
      'emailType' => 'pingWebhook',
      'stripeEventId' => 'EV_00000000'
    ];
    $mailQueueId = $this->stripeWebhookEmails->queueEmail($mailInfo);
    $this->stripeWebhookEmails->sendEmail($mailInfo);
    $this->assertTrue(true);
  }
}
