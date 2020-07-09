<?php
namespace Framework\Mail;
use Framework\Contracts\MailContract;
use Aws\Ses\SesClient;

class AwsSesMail implements MailContract {
  protected $client;

  public function __construct($aws_ses_key, $aws_ses_secret, $aws_region) {
    $this->client = SesClient::factory(array(
      'credentials' => array(
        'key'    => $aws_ses_key,
        'secret' => $aws_ses_secret,
      ),
      'version' => 'latest',
      'region'  => $aws_region
    ));
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
    try {
      $msg = array();
      $msg['Source'] = $from;
      // ToAddresses must be an array
      $to_array = explode(",", $to);
      $msg['Destination']['ToAddresses'] = $to_array;
      $msg['Message']['Subject']['Data'] = $subject;
      $msg['Message']['Subject']['Charset'] = "UTF-8";
      $msg['Message']['Body']['Text']['Data'] = $body_text;
      $msg['Message']['Body']['Text']['Charset'] = "UTF-8";
      $msg['Message']['Body']['Html']['Data'] = $body_html;
      $msg['Message']['Body']['Html']['Charset'] = "UTF-8";
      $msg['ReplyToAddresses'][] = $reply;

      try {
        $sendresult = $this->client->sendEmail($msg);
        $aws_message_id = $sendresult['MessageId'];
      }
      catch( Exception $e ) {
        $sendEmailResult = [
          'status' => 'error',
          'message' => $e->getMessage(),
          'aws_message_id' => 0
        ];
        return $sendEmailResult;
      }
    }
    catch( Exception $e ) {
      $sendEmailResult = [
        'status' => 'error',
        'message' => $e->getMessage(),
        'aws_message_id' => 0
      ];
      return $sendEmailResult;
    }
    $sendEmailResult = [
      'status' => 'success',
      'message' => 'Message sent successfully',
      'aws_message_id' => $aws_message_id
    ];
    return $sendEmailResult;
  }
}
