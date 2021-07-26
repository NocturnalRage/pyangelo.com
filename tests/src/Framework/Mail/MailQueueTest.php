<?php
namespace tests\src\Framework\Mail;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Mail\MailQueue;
use IvoPetkov\HTML5DOMDocument;

class MailQueueTest extends TestCase {
  public function tearDown(): void {
    Mockery::close();
  }

  public function testProcessTransactionalQueue() {
    $mail_queue_transactional_id_1 = 1;
    $mail_queue_transactional_id_2 = 2;
    $fromEmail = 'admin@nocturnalrage.com';
    $emails = [
      [
        'mail_queue_transactional_id' => $mail_queue_transactional_id_1,
        'from_email' => $fromEmail,
        'reply_email' => $fromEmail,
        'to_email' => 'anyone@example.com',
        'subject' => 'Email 1',
        'body_text' => 'Body text',
        'body_html' => 'Body HTML'
      ],
      [
        'mail_queue_transactional_id' => $mail_queue_transactional_id_2,
        'from_email' => $fromEmail,
        'reply_email' => $fromEmail,
        'to_email' => 'anyone@example.com',
        'subject' => 'Email 2',
        'body_text' => 'Body text 2',
        'body_html' => 'Body HTML 2'
      ],
    ];
    $awsMessageId = 'test-aws-message-id';
    $sendEmailResult = [
      'status' =>'success',
      'aws_message_id' => $awsMessageId
    ];
    $expectedSubject = 'Hello Fast';
    $repository = Mockery::mock('PyAngelo\Repositories\MailRepository');
    $repository->shouldReceive('getQueuedTransactionalMail')
      ->once()->with(100)->andReturn($emails);
    $repository->shouldReceive('setEmailStatus')
      ->once()->with(2, $mail_queue_transactional_id_1);
    $repository->shouldReceive('setEmailStatus')
      ->once()->with(2, $mail_queue_transactional_id_2);
    $mailer = Mockery::mock('Framework\Mail\LoggerMail');
    $mailer->shouldReceive('send')
      ->twice()
      ->with(
        $fromEmail,
        $fromEmail,
        Mockery::any(),
        Mockery::any(),
        Mockery::any(),
        Mockery::any(),
        'UTF-8'
      )
      ->andReturn($sendEmailResult);

    $mailQueue = new MailQueue($repository, $mailer);
    $mailQueue->processTransactionalQueue();
    $this->assertTrue(true);
  }
}
