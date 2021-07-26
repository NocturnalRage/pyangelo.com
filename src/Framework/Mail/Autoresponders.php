<?php
namespace Framework\Mail;
use Framework\Contracts\MailContract;
use PyAngelo\Repositories\CampaignRepository;
use IvoPetkov\HTML5DOMDocument; 

class Autoresponders {
  const PERSON_KEY = 82351832; // Random key for use in encryption
  const AUTORESPONDER_KEY = 91832348; // Random key for use in encryption
  const LINK_KEY = 51234189; // Random key for use in encryption

  const SENT_STATUS = 3;

  const SENT_ACTIVITY = 1;
  const OPENED_ACTIVITY = 2;
  const BOUNCED_ACTIVITY = 3;
  const COMPLAINED_ACTIVITY = 4;
  const CLICKED_ACTIVITY = 5;
  const UNSUBSCRIBED_ACTIVITY = 6;

  protected $campaignRepository;
  protected $mailer;
  protected $dom;
  protected $emailDomain;

  protected $fromEmail;
  protected $replyEmail;
  protected $toEmail;
  protected $subject;
  protected $bodyHtml;
  protected $bodyText;

  public function __construct(
    CampaignRepository $campaignRepository,
    MailContract $mailer,
    HTML5DOMDocument $dom,
    $emailDomain
  ) {
    $this->campaignRepository = $campaignRepository;
    $this->mailer = $mailer;
    $this->dom = $dom;
    $this->emailDomain = $emailDomain;
  }

  public function sendOutstanding() {
    $logDate = date('Y-m-d H:i:s');
    $message = $logDate . ": Begin Sending Outstanding Autoresponders\n";
    file_put_contents($_ENV['APPLICATION_LOG_FILE'], $message, FILE_APPEND);
    $autoresponders = $this->campaignRepository->getActiveAutoresponders();
    foreach ($autoresponders as $autoresponder) {
      $this->setFromAddress($autoresponder['from_email']);
      $subscribers = $this->campaignRepository->getAutoresponderSubscribers(
        $autoresponder['delay_in_minutes'],
        $autoresponder['list_id'],
        $autoresponder['autoresponder_where_condition']
      );
      foreach ($subscribers as $subscriber) {
        $this->personaliseEmail($autoresponder, $subscriber);

        $sendEmailResult = $this->mailer->send(
          $this->fromEmail,
          $this->replyEmail,
          $this->toEmail,
          $this->subject,
          $this->bodyText,
          $this->bodyHtml,
          'UTF-8'
        );

        if ($sendEmailResult['status'] == 'success') {
          $this->campaignRepository->recordAutoresponderActivity(
            self::SENT_ACTIVITY,
            $autoresponder['autoresponder_id'],
            $subscriber['person_id'],
            $sendEmailResult['aws_message_id']
          );
          $this->campaignRepository->updateLastAutoresponder(
            $subscriber['person_id'],
            $autoresponder['list_id']
          );
        }
        $message = "Sent autoresponder: {$this->subject} : {$this->toEmail}\n";
        file_put_contents($_ENV['APPLICATION_LOG_FILE'], $message, FILE_APPEND);
      }
    }
    $logDate = date('Y-m-d H:i:s');
    $message = $logDate . ": Finished Sending Outstanding Autoresponders\n";
    file_put_contents($_ENV['APPLICATION_LOG_FILE'], $message, FILE_APPEND);
  }

  public function sendTest($autoresponderId, $toEmail) {
    if (! $autoresponder = $this->campaignRepository->getAutoresponderById($autoresponderId)) {
      return false;
    }
    $this->setFromAddress($autoresponder['from_email']);
    $subscriber = [
      'person_id' => 2,
      'given_name' => 'Erno',
      'family_name' => 'Rubik',
      'email' => $toEmail,
      'created_at' => '2017-01-01 00:00:00',
      'last_campaign_at' => '2017-01-01 00:00:00',
      'last_autoresponder_at' => '2017-01-01 00:00:00'
    ];
    $this->personaliseEmail($autoresponder, $subscriber);
    $sendEmailResult = $this->mailer->send(
      $this->fromEmail,
      $this->replyEmail,
      $this->toEmail,
      $this->subject,
      $this->bodyText,
      $this->bodyHtml,
      'UTF-8'
    );
    return true;
  }

  public function recordOpen($autoresponderKey, $personKey) {
    $autoresponderId = $this->decryptNumber($autoresponderKey, self::AUTORESPONDER_KEY);
    $personId = $this->decryptNumber($personKey, self::PERSON_KEY);
    $this->campaignRepository->recordAutoresponderActivity(
      self::OPENED_ACTIVITY,
      $autoresponderId,
      $personId
    );
  }

  public function recordClick($autoresponderKey, $personKey, $linkKey) {
    $autoresponderId = $this->decryptNumber($autoresponderKey, self::AUTORESPONDER_KEY);
    $personId = $this->decryptNumber($personKey, self::PERSON_KEY);
    $linkId = $this->decryptNumber($linkKey, self::LINK_KEY);
    if ($storedLink = $this->campaignRepository->getTrackableLinkById($linkId)) {
      $this->campaignRepository->recordAutoresponderActivity(
        self::CLICKED_ACTIVITY,
        $autoresponderId,
        $personId,
        NULL,
        $linkId
      );
      return $storedLink['href'];
    }
    return false;
  }

  public function unsubscribe($autoresponderKey, $personKey, $unsubscribeHash) {
    $autoresponderId = $this->decryptNumber($autoresponderKey, self::AUTORESPONDER_KEY);
    $personId = $this->decryptNumber($personKey, self::PERSON_KEY);
    $person = $this->campaignRepository->getPersonById($personId);
    $unsubscribeCheck = md5($person['given_name'] . $person['family_name'] . $person['created_at']);
    if ($unsubscribeCheck == $unsubscribeHash) {
      $this->campaignRepository->unsubscribeFromAllLists($personId);
      $this->campaignRepository->recordAutoresponderActivity(
        self::UNSUBSCRIBED_ACTIVITY,
        $autoresponderId,
        $personId
      );
      return true;
    }
    return false;
  }

  public function prepareWebVersion($autoresponderKey, $personKey, $personHash) {
    $autoresponderId = $this->decryptNumber($autoresponderKey, self::AUTORESPONDER_KEY);
    if (! $autoresponder = $this->campaignRepository->getAutoresponderById($autoresponderId)) {
      return false;
    }
    $personId = $this->decryptNumber($personKey, self::PERSON_KEY);
    $subscriber = $this->campaignRepository->getSubscriberByPersonId($personId);
    $personCheck = md5($subscriber['given_name'] . $subscriber['family_name'] . $subscriber['created_at']);
    if ($personCheck == $personHash) {
      $this->personaliseEmail($autoresponder, $subscriber);
      return [
        'autoresponder_id' => $autoresponderId,
        'subject' => $this->subject,
        'body_html' => $this->bodyHtml
      ];
    }
  }

  private function setFromAddress($fromEmail) {
    $this->fromEmail = $fromEmail;
    $this->replyEmail = $fromEmail;
  }

  private function personaliseEmail($autoresponder, $subscriber) {
    $uniqueHash = md5(
      $subscriber['given_name'] . $subscriber['family_name'] . $subscriber['created_at']
    );
    $autoresponderKey = $this->encryptNumber(
      $autoresponder['autoresponder_id'], self::AUTORESPONDER_KEY
    );
    $personKey = $this->encryptNumber(
      $subscriber['person_id'], self::PERSON_KEY
    );

    $this->toEmail = $subscriber['email'];
    $this->updateGivenName($autoresponder, $subscriber['given_name']);
    $this->insertTrackableLinks($autoresponderKey, $personKey);
    $this->insertUnsubscribeLinks($autoresponderKey, $personKey, $uniqueHash);
    $this->insertShowInBrowserLinks($autoresponderKey, $personKey, $uniqueHash);
    $this->addOpenDetectorImage($autoresponderKey, $personKey);
  }

  private function updateGivenName($autoresponder, $givenName) {
    $this->subject = preg_replace(
      '/\[givenname\]/',
      $givenName,
      $autoresponder['subject']
    );

    $this->bodyHtml = preg_replace(
      '/\[givenname\]/',
      $givenName,
      $autoresponder['body_html']
    );

    $this->bodyText = preg_replace(
      '/\[givenname\]/',
      $givenName,
      $autoresponder['body_text']
    );
  }

  private function insertTrackableLinks($autoresponderKey, $personKey) {
    $this->dom->loadHTML($this->bodyHtml);

    foreach ($this->dom->getElementsByTagName('a') as $anchor) {
      $href = $anchor->getAttribute('href');
      if ($storedLink = $this->campaignRepository->getTrackableLink($href)) {
        $linkId = $storedLink['link_id'];
      }
      else {
        $linkId = $this->campaignRepository->createTrackableLink($href);
      }
      $linkKey = $this->encryptNumber($linkId, self::LINK_KEY);
      $trackableLink = $this->emailDomain . '/autoresponder/links/' .
        $autoresponderKey . '/' . $personKey . '/' . $linkKey;
      $anchor->setAttribute('href', $trackableLink);
    }
    $this->bodyHtml = $this->dom->saveHTML();
  }

  private function insertUnsubscribeLinks(
    $autoresponderKey,
    $personKey,
    $uniqueHash
  ) {
    $unsubscribeLink = $this->emailDomain . '/autoresponder/unsubscribe/' .
      $autoresponderKey . '/' . $personKey . '/' . $uniqueHash;
    $unsubscribeHtml = '<a href="' . $unsubscribeLink . '">Unsubscribe instantly.</a>';
    $this->bodyText = preg_replace(
      '/\[unsubscribe\]/',
      $unsubscribeLink,
      $this->bodyText
    );
    $this->bodyHtml = preg_replace(
      '/\[unsubscribe\]/',
      $unsubscribeHtml,
      $this->bodyHtml
    );
  }

  private function insertShowInBrowserLinks(
    $autoresponderKey,
    $personKey,
    $uniqueHash
  ) {
    $showInBrowserLink = $this->emailDomain . '/autoresponder/display/' .
      $autoresponderKey . '/' . $personKey . '/' . $uniqueHash;
    $showInBrowserHtml = '<a href="' . $showInBrowserLink . '">View in your browser</a>';
    $this->bodyText = preg_replace(
      '/\[showinbrowser\]/',
      $showInBrowserLink,
      $this->bodyText
    );
    $this->bodyHtml = preg_replace(
      '/\[showinbrowser\]/',
      $showInBrowserHtml,
      $this->bodyHtml
    );
  }
  private function addOpenDetectorImage($autoresponderKey, $personKey) {
    $openImage = '<img src="' . $this->emailDomain . '/autoresponder/open/' .
      $autoresponderKey . '/' . $personKey . '" width="1" height="1"  border="0" /></body>';
    $this->bodyHtml = preg_replace('/<\/body>/', $openImage, $this->bodyHtml);
  }

  /**       
   * Encrypt decimal number
   * @param $number (max value 999.999.999)
   * @return string
   */

  private function encryptNumber($number, $key) {
    $key = str_pad(decbin($key), 32, "0", STR_PAD_LEFT);
    $number = str_pad(decbin($number), 32, "0", STR_PAD_LEFT);
    $bit = $this->bitxor($key, $number);
    $dec = bindec($bit);
    $base36 = base_convert($dec,10,36);
    return strtoupper($base36);
  }

  /**
   * Decrypt an encrypted decimal number
   * @param $String
   * @return bool|number|string
   */
  private function decryptNumber($string, $key) {
    $dec = base_convert($string, 36, 10);
    $key = str_pad(decbin($key), 32, "0", STR_PAD_LEFT);
    $string = str_pad(decbin($dec), 32, "0", STR_PAD_LEFT);
    $bit = $this->bitxor($key, $string);
    $dec = bindec($bit);
    return $dec;
  }

  private function bitxor($o1, $o2) {
    $xorWidth = PHP_INT_SIZE*8;
    $o1 = str_split($o1, $xorWidth);
    $o2 = str_split($o2, $xorWidth);
    $res = '';
    $runs = count($o1);
    for($i=0;$i<$runs;$i++)
      $res .= decbin(bindec($o1[$i]) ^ bindec($o2[$i]));
    return $res;
  }

  public function bodyHtml() {
    return $this->bodyHtml;
  }

  public function bodyText() {
    return $this->bodyText;
  }

  public function subject() {
    return $this->subject;
  }
}
?>
