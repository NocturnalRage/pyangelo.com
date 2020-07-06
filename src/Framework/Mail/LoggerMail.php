<?php
namespace Framework\Mail;
use Framework\Contracts\MailContract;

class LoggerMail implements MailContract {
  protected $logFile;

  public function __construct($logFile) {
    $this->logFile = $logFile;
  }

  public function send(
    $from,
    $reply,
    $to,
    $subject,
    $body_text,
    $body_html,
    $charset
  ) {
    $logDate = date('Y-m-d H:i:s');
    $message = "************************************************" . PHP_EOL .
               "$logDate: Logging Email That Would Be Sent " . PHP_EOL .
               "From: " . $from . PHP_EOL .
               "Reply: " . $reply . PHP_EOL .
               "To: " . $to . PHP_EOL .
               "Subject: " . $subject . PHP_EOL .
               "Body Text: " . $body_text . PHP_EOL .
               "Body Html: " . $body_html . PHP_EOL .
               "Charset: " . $charset . PHP_EOL .
               "************************************************" . PHP_EOL;
    $bytes = file_put_contents($this->logFile, $message, FILE_APPEND);
    $sendEmailResult = [
      'status' => 'success',
      'message' => 'Message sent successfully',
      'aws_message_id' => 0
    ];
    return $sendEmailResult;
  }
}
