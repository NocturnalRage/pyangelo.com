<?php
namespace Framework\Mail;
use Aws\Sqs\SqsClient;
use PyAngelo\Repositories\PersonRepository;
use PyAngelo\Repositories\CampaignRepository;

class AwsSqsMailProcessor {
  const ACTIVE = 1;
  const BOUNCED = 2;
  const COMPLAINED = 3;

  const MAX_BOUNCE_COUNT = 10;

  const BOUNCED_ACTIVITY = 3;
  const COMPLAINED_ACTIVITY = 4;

  protected $client;
  protected $personRepository;
  protected $campaignRepository;

  public function __construct(
    $aws_sqs_key,
    $aws_sqs_secret,
    $aws_region,
    $bounceUrl,
    $complaintUrl,
    PersonRepository $personRepository,
    CampaignRepository $campaignRepository
  ) {
    $this->client = SqsClient::factory(array(
      'credentials' => array(
        'key'    => $aws_sqs_key,
        'secret' => $aws_sqs_secret,
      ),
      'version' => 'latest',
      'region'  => $aws_region
    ));
    $this->bounceUrl = $bounceUrl;
    $this->complaintUrl = $complaintUrl;
    $this->personRepository = $personRepository;
    $this->campaignRepository = $campaignRepository;
  }

  public function processBounceQueue() {
    $logDate = date('Y-m-d H:i:s');
    echo $logDate . ": Processing Bounces\n";

    $result = $this->client->receiveMessage(array(
      'QueueUrl' => $this->bounceUrl,
      'MaxNumberOfMessages' => 10,
    ));
    $messages = $result->get('Messages');
    if ($messages) {
      foreach ($messages as $message) {
        echo "-------------------------------------------\n";
        $this->processBounce($message);
        echo "-------------------------------------------\n";
      }
    }
  }

  public function processComplaintQueue() {
    $logDate = date('Y-m-d H:i:s');
    echo $logDate . ": Processing Complaints\n";

    $result = $this->client->receiveMessage(array(
      'QueueUrl' => $this->complaintUrl,
      'MaxNumberOfMessages' => 10,
    ));
    $messages = $result->get('Messages');
    if ($messages) {
      foreach ($messages as $message) {
        echo "-------------------------------------------\n";
        $this->processComplaint($message);
        echo "-------------------------------------------\n";
      }
    }
  }

  private function processBounce($message) {
    $receiptHandle = $message['ReceiptHandle'];
    $bodyMessage = json_decode($message['Body'], TRUE);
    $actualMessage = json_decode($bodyMessage['Message'], TRUE);
    $notificationType = $actualMessage['notificationType'];
    $messageId = $actualMessage['mail']['messageId'];
    $bounceType = $actualMessage['bounce']['bounceType'];
    $bounceSubType = $actualMessage['bounce']['bounceSubType'];
    $bounceTimestamp = str_replace('T', ' ', substr($actualMessage['bounce']['timestamp'], 0, 19));
    if ($bounceType == 'Undetermined' || $bounceType == 'Permanent') {
      $action = 'Unsubscribe';
    }
    else if ($bounceType == 'Transient' && $bounceSubType == 'General') {
      // This was most likely an out of office bounce.
      $action = 'None';
    }
    else {
      $action = 'Record';
    }
    $emailAddresses = $actualMessage['bounce']['bouncedRecipients'];
       
    foreach ($emailAddresses as $emailAddress) {
      echo "Action To Take: " . $action . "\n";
      echo "Email Address: " . $emailAddress['emailAddress'] . "\n";
      echo "Notification Type: " . $notificationType . "\n";
      echo "Message Id: " . $messageId . "\n";
      echo "Bounce Type: " . $bounceType . "\n";
      echo "Bounce Sub Type: " . $bounceSubType . "\n";
      echo "Bounce Timestamp: " . $bounceTimestamp . "\n";

      $person = $this->personRepository->getPersonActiveOrNotByEmail(
        $emailAddress['emailAddress']
      );
      if ($person) {
        $personId = $person['person_id'];
        echo "Person Id: $personId\n";
        if ($action == 'Unsubscribe' || $action == 'Record') {
          $this->personRepository->incrementBounceCount($personId);
          $this->recordBounce($personId, $messageId, $bounceType, $bounceSubType);
        }
        if ($action == 'Unsubscribe' || $this->tooManyBounces($personId)) {
          $this->personRepository->setEmailStatus($personId, self::BOUNCED);
          $this->campaignRepository->unsubscribeFromAllLists($personId);
        }
      }
    }

    // Delete message from queue.
    $deleteResult = $this->client->deleteMessage(array(
      'QueueUrl' => $this->bounceUrl,
      'ReceiptHandle' => $receiptHandle,
    ));
  }

  private function processComplaint($message) {
    $receiptHandle = $message['ReceiptHandle'];
    $bodyMessage = json_decode($message['Body'], TRUE);
    $actualMessage = json_decode($bodyMessage['Message'], TRUE);
    $notificationType = $actualMessage['notificationType'];
    $messageId = $actualMessage['mail']['messageId'];
    $complaintTimestamp = str_replace('T', ' ', substr($actualMessage['complaint']['timestamp'], 0, 19));
    $complaintFeedbackType = $actualMessage['complaint']['complaintFeedbackType'];

    $emailAddresses = $actualMessage['complaint']['complainedRecipients'];
       
    foreach ($emailAddresses as $emailAddress) {
      $person = $this->personRepository->getPersonActiveOrNotByEmail(
        $emailAddress['emailAddress']
      );
      if ($person) {
        $personId = $person['person_id'];
        echo "Person Id: $personId\n";
        if ($complaintFeedbackType == "not-spam") {
          $this->personRepository->setEmailStatus($personId, self::ACTIVE);
        }
        else {
          $this->personRepository->setEmailStatus($personId, self::COMPLAINED);
          $this->campaignRepository->unsubscribeFromAllLists($personId);
          $this->recordComplaint($personId, $messageId);
        }
      }
      echo "Email Address: " . $emailAddress['emailAddress'] . "\n";
      echo "Notification Type: " . $notificationType . "\n";
      echo "Message Id: " . $messageId . "\n";
      echo "Complaint Feedback Type: " . $complaintFeedbackType . "\n";
      echo "Complaint Timestamp: " . $complaintTimestamp . "\n";
    }

    // Delete message from queue.
    $deleteResult = $this->client->deleteMessage(array(
      'QueueUrl' => $this->complaintUrl,
      'ReceiptHandle' => $receiptHandle,
    ));
  }

  private function tooManyBounces($personId) {
    $person = $this->personRepository->getPersonById($personId);
    return $person['bounce_count'] > self::MAX_BOUNCE_COUNT;
  }

  private function recordBounce($personId, $messageId, $bounceType, $bounceSubType) {
    $bounceTypeId = $this->setBounceTypeId($bounceType, $bounceSubType);
    if ($sentActivity = $this->campaignRepository->getSentCampaignActivity($messageId)) {
      $this->campaignRepository->recordCampaignActivity(
        self::BOUNCED_ACTIVITY,
        $sentActivity['campaign_id'],
        $personId,
        NULL,
        NULL,
        $bounceTypeId
      );
    }
    elseif ($sentActivity = $this->campaignRepository->getSentAutoresponderActivity($messageId)) {
      $this->campaignRepository->recordAutoresponderActivity(
        self::BOUNCED_ACTIVITY,
        $sentActivity['autoresponder_id'],
        $personId,
        NULL,
        NULL,
        $bounceTypeId
      );
    }
  }

  private function recordComplaint($personId, $messageId) {
    if ($sentActivity = $this->campaignRepository->getSentCampaignActivity($messageId)) {
      $this->campaignRepository->recordCampaignActivity(
        self::COMPLAINED_ACTIVITY,
        $sentActivity['campaign_id'],
        $personId
      );
    }
    elseif ($sentActivity = $this->campaignRepository->getSentAutoresponderActivity($messageId)) {
      $this->campaignRepository->recordAutoresponderActivity(
        self::COMPLAINED_ACTIVITY,
        $sentActivity['autoresponder_id'],
        $personId
      );
    }
  }

  private function setBounceTypeId($bounceType, $bounceSubType) {
    $bounceTypeId = 0;
    if ($bounceType == 'Undetermined')
      $bounceTypeId = 1;
    else if ($bounceType == 'Permanent' && $bounceSubType == 'General')
      $bounceTypeId = 2;
    else if ($bounceType == 'Permanent' && $bounceSubType == 'NoEmail')
      $bounceTypeId = 3;
    else if ($bounceType == 'Permanent' && $bounceSubType == 'Suppressed')
      $bounceTypeId = 4;
    else if ($bounceType == 'Transient' && $bounceSubType == 'General')
      $bounceTypeId = 5;
    else if ($bounceType == 'Transient' && $bounceSubType == 'MailboxFull')
      $bounceTypeId = 6;
    else if ($bounceType == 'Transient' && $bounceSubType == 'MessageTooLarge')
      $bounceTypeId = 7;
    else if ($bounceType == 'Transient' && $bounceSubType == 'ContentRejected')
      $bounceTypeId = 8;
    else if ($bounceType == 'Transient' && $bounceSubType == 'AttachmentRejected')
      $bounceTypeId = 9;
    else
      $bounceTypeId = 1;
    return $bounceTypeId;
  }
}
?>
