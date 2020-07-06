<?php
namespace Framework\Contracts;

interface MailContract {
  public function send(
    $from,
    $reply,
    $to,
    $subject,
    $body_text,
    $body_html,
    $charset
  );
}
