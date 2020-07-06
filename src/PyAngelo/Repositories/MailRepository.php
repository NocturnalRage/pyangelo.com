<?php
namespace PyAngelo\Repositories;

interface MailRepository {

  public function getTransactionalMailById($id);

  public function getQueuedTransactionalMail($limit);

  public function insertTransactionalMail(
    $fromEmail, $replyEmail, $toEmail, $subject, $bodyText, $bodyHtml
  );

  public function setEmailStatus($statusId, $mailQueueId);

  public function deleteTransactionalMailById($mailQueueTransactionalId);

  public function deleteAllMailQueueTransactional();
}
?>
