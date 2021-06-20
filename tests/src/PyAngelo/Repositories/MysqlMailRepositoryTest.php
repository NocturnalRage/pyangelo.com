<?php
namespace Tests\PyAngelo\Repositories;

use PHPUnit\Framework\TestCase;
use PyAngelo\Repositories\MysqlMailRepository;

class MysqlMailRepositoryTest extends TestCase {
  protected $dbh;
  protected $mailRepository;

  public function setUp(): void {
    $dotenv  = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../../../', '.env.test');
    $dotenv->load();
    $this->dbh = new \Mysqli(
      $_ENV['DB_HOST'],
      $_ENV['DB_USERNAME'],
      $_ENV['DB_PASSWORD'],
      $_ENV['DB_DATABASE']
    );
    $this->mailRepository = new MysqlMailRepository($this->dbh);
  }

  public function tearDown(): void {
    $this->dbh->close();
  }

  public function testInsertTransactionalMailAndGetTransactionalMailById() {
    $deleted = $this->mailRepository->deleteAllMailQueueTransactional();
    $this->assertTrue($deleted);

    $fromEmail = 'admin@nocturnalrage.com';
    $replyEmail = 'admin@nocturnalrage.com';
    $toEmail = 'fred@hotmail.com';
    $subject = 'PyAngelo Website';
    $bodyText = 'It looks good';
    $bodyHtml = '<p>It looks good</p>';
    $expectedDeletedCount = 1;

    // Insert, retrieve, and delete data from the table
    $mailQueueTransactionalId = $this->mailRepository->insertTransactionalMail(
      $fromEmail, $replyEmail, $toEmail, $subject, $bodyText, $bodyHtml
    );
    $mail = $this->mailRepository->getTransactionalMailById(
      $mailQueueTransactionalId
    );
    $emails = $this->mailRepository->getQueuedTransactionalMail(10);
    $this->assertCount(1, $emails);
    $this->mailRepository->setEmailStatus(2, $emails[0]['mail_queue_transactional_id']);
    $emails = $this->mailRepository->getQueuedTransactionalMail(10);
    $this->assertCount(0, $emails);

    $deletedCount = $this->mailRepository->deleteTransactionalMailById($mailQueueTransactionalId);

    $this->assertSame($fromEmail, $mail['from_email']);
    $this->assertSame($toEmail, $mail['to_email']);
    $this->assertSame($subject, $mail['subject']);
    $this->assertSame($bodyText, $mail['body_text']);
    $this->assertSame($bodyHtml, $mail['body_html']);
    $this->assertSame(1, $mail['mail_queue_status_id']);
    $this->assertSame($expectedDeletedCount, $deletedCount);
  }
}
