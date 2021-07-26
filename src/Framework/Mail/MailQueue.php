<?php
namespace Framework\Mail;
use Framework\Contracts\MailContract;
use PyAngelo\Repositories\MailRepository;

class MailQueue {
  const QUEUED = 1;
  const SENT = 2;
  const FAILED = 3;
  const MAIL_COUNT = 100;

  protected $mailRepository;
  protected $mailer;

  public function __construct(
    MailRepository $mailRepository,
    MailContract $mailer
  ) {
    $this->mailRepository = $mailRepository;
    $this->mailer = $mailer;
  }

  public function processTransactionalQueue() {
    $emails = $this->mailRepository->getQueuedTransactionalMail(
      self::MAIL_COUNT
    );
    foreach ($emails as $email) {
      $sendEmailResult = $this->mailer->send(
        $email['from_email'],
        $email['reply_email'],
        $email['to_email'],
        $email['subject'],
        $email['body_text'],
        $email['body_html'],
        'UTF-8'
      );

      if ($sendEmailResult['status'] == 'success') {
        $status = self::SENT;
      }
      else {
        $status = self::FAILED;
      }
      $rowsUpdated = $this->mailRepository->setEmailStatus(
        $status,
        $email['mail_queue_transactional_id']
      );
    }
  }
}
?>
