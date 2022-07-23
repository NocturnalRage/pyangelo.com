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
    $this->dbh->begin_transaction();
    $this->mailRepository = new MysqlMailRepository($this->dbh);
  }

  public function tearDown(): void {
    $this->dbh->rollback();
    $this->dbh->close();
  }

  public function testMailRepositoryFunctions() {
    $fromEmail = 'admin@nocturnalrage.com';
    $replyEmail = 'admin@nocturnalrage.com';
    $toEmail = 'fred@hotmail.com';
    $subject = 'PyAngelo Website';
    $bodyText = 'It looks good';
    $bodyHtml = '<p>It looks good</p>';

    // Insert, retrieve, and delete data from the table
    $mailQueueTransactionalId = $this->mailRepository->insertTransactionalMail(
      $fromEmail, $replyEmail, $toEmail, $subject, $bodyText, $bodyHtml
    );
    $mail = $this->mailRepository->getTransactionalMailById(
      $mailQueueTransactionalId
    );
    $this->assertEquals($fromEmail, $mail['from_email']);
    $this->assertEquals($replyEmail, $mail['reply_email']);
    $this->assertEquals($toEmail, $mail['to_email']);
    $this->assertEquals($subject, $mail['subject']);
    $this->assertEquals($bodyText, $mail['body_text']);
    $this->assertEquals($bodyHtml, $mail['body_html']);

    $emails = $this->mailRepository->getQueuedTransactionalMail(10);
    $this->assertCount(1, $emails);
    $this->assertEquals($subject, $emails[0]['subject']);

    $this->mailRepository->setEmailStatus(2, $emails[0]['mail_queue_transactional_id']);
    $emails = $this->mailRepository->getQueuedTransactionalMail(10);
    $this->assertCount(0, $emails);
  }
}
