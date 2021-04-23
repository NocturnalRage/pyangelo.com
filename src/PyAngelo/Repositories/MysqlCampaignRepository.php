<?php
namespace PyAngelo\Repositories;

class MysqlCampaignRepository implements CampaignRepository {
  const DRAFT_STATUS = 1;
  const SENDING_STATUS = 2;
  const SENT_STATUS = 3;

  protected $dbh;

  public function __construct(\Mysqli $dbh) {
    $this->dbh = $dbh;
  }

  public function getDraftCampaigns() {
    $sql = "SELECT c.*, s.segment_name, fe.email as from_email
            FROM   campaign c
            JOIN   segment s ON s.segment_id = c.segment_id
            JOIN   from_email fe on fe.from_email_id = c.from_email_id
            WHERE  c.campaign_status_id = " . self::DRAFT_STATUS;
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getSentCampaigns() {
    $sql = "SELECT c.campaign_id, c.subject,
                   s.segment_name,
                   date_format(c.updated_at, '%d %b %Y') sent_at,
                   sum(case when ca.activity_type_id = 1 then 1 else 0 end) as recipients,
                   count(distinct case when ca.activity_type_id in (2, 5) then ca.person_id else NULL end) as opened,
                   count(distinct case when ca.activity_type_id = 3 then ca.person_id else NULL end) as bounced,
                   count(distinct case when ca.activity_type_id = 4 then ca.person_id else NULL end) as complained,
                   count(distinct case when ca.activity_type_id = 5 then ca.person_id else NULL end) as clicked,
                   count(distinct case when ca.activity_type_id = 6 then ca.person_id else NULL end) as unsubscribed
            FROM   campaign c
            JOIN   segment s ON s.segment_id = c.segment_id
            JOIN   from_email fe on fe.from_email_id = c.from_email_id
            LEFT JOIN   campaign_activity ca on ca.campaign_id = c.campaign_id
            WHERE  c.campaign_status_id = " . self::SENT_STATUS . "
            GROUP BY c.campaign_id
            ORDER BY c.updated_at DESC";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getCampaignStats($campaignId) {
    $sql = "SELECT c.campaign_id, c.subject,
                   s.segment_name,
                   date_format(c.updated_at, '%d %b %Y') sent_at,
                   sum(case when ca.activity_type_id = 1 then 1 else 0 end) as recipients,
                   count(distinct case when ca.activity_type_id in (2, 5) then ca.person_id else NULL end) as opened,
                   count(distinct case when ca.activity_type_id = 3 then ca.person_id else NULL end) as bounced,
                   count(distinct case when ca.activity_type_id = 4 then ca.person_id else NULL end) as complained,
                   count(distinct case when ca.activity_type_id = 5 then ca.person_id else NULL end) as clicked,
                   count(distinct case when ca.activity_type_id = 6 then ca.person_id else NULL end) as unsubscribed
            FROM   campaign c
            JOIN   segment s ON s.segment_id = c.segment_id
            JOIN   from_email fe on fe.from_email_id = c.from_email_id
            LEFT JOIN   campaign_activity ca on ca.campaign_id = c.campaign_id
            WHERE  c.campaign_id = ?
            GROUP BY c.campaign_id";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $campaignId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getCampaignById($campaignId) {
    $sql = "SELECT c.*, s.segment_name, fe.email as from_email
	        FROM   campaign c
            JOIN   segment s ON s.segment_id = c.segment_id
            JOIN   from_email fe on fe.from_email_id = c.from_email_id
            WHERE  c.campaign_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $campaignId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getAllFromEmails() {
    $sql = "SELECT *
            FROM   from_email";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getAllLists() {
    $sql = "SELECT *
            FROM   list";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getAllSegments() {
    $sql = "SELECT *
            FROM   segment";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getListById($listId) {
    $sql = "SELECT *
	        FROM   list
            WHERE  list_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $listId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getSegmentById($segmentId) {
    $sql = "SELECT *
	        FROM   segment
            WHERE  segment_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $segmentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getFromEmailById($fromEmailId) {
    $sql = "SELECT *
	        FROM   from_email
            WHERE  from_email_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $fromEmailId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function insertDraftCampaign($formData) {
    $sql = "INSERT INTO campaign (
              campaign_id,
              campaign_status_id,
              segment_id,
              from_email_id,
              subject,
              body_text,
              body_html,
              created_at,
              updated_at
            )
            VALUES (NULL, " . self::DRAFT_STATUS . ", ?, ?, ?, ?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'iisss',
      $formData['segment_id'],
      $formData['from_email_id'],
      $formData['subject'],
      $formData['body_text'],
      $formData['body_html']
    );
    $stmt->execute();
    $stmt->close();
    return $this->dbh->insert_id;
  }

  public function getCampaignsToBeSent() {
    $sql = "SELECT c.*, s.*, fe.email as from_email
            FROM   campaign c
            JOIN   segment s ON s.segment_id = c.segment_id
            JOIN   from_email fe on fe.from_email_id = c.from_email_id
            WHERE  c.campaign_status_id = " . self::SENDING_STATUS;
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getActiveSubscribersFromList($listId) {
    $sql = "SELECT p.person_id, p.given_name, p.family_name,
                   p.email, p.created_at,
                   s.last_campaign_at, s.last_autoresponder_at
            FROM   subscriber s
            JOIN   person p ON p.person_id = s.person_id
            WHERE  s.list_id = ?
            AND    s.subscriber_status_id = 1";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $listId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getSubscribers($listId, $whereCondition) {
    $sql = "SELECT p.person_id, p.given_name, p.family_name,
                   p.email, p.created_at,
                   s.last_campaign_at, s.last_autoresponder_at
            FROM   subscriber s
            JOIN   person p ON p.person_id = s.person_id
            WHERE  s.list_id = ?
            AND    s.subscriber_status_id = 1
            AND    {$whereCondition}";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $listId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getSubscriberByPersonId($personId) {
    $sql = "SELECT p.person_id, p.given_name, p.family_name,
                   p.email, p.created_at,
                   s.last_campaign_at, s.last_autoresponder_at
            FROM   subscriber s
            JOIN   person p ON p.person_id = s.person_id
            WHERE  p.person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getTrackableLink($href) {
    $sql = "SELECT *
            FROM   trackable_link
            WHERE  href = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $href);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getTrackableLinkById($linkId) {
    $sql = "SELECT *
            FROM   trackable_link
            WHERE  link_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $linkId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function createTrackableLink($href) {
    $sql = "INSERT INTO trackable_link (link_id, href)
            VALUES (NULL, ?)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $href);
    $stmt->execute();
    $linkId = $this->dbh->insert_id;
    $stmt->close();
    return $linkId;
  }

  public function recordCampaignActivity(
    $activityTypeId,
    $campaignId,
    $personId,
    $awsMessageId = NULL,
    $linkId = NULL,
    $bounceTypeId = NULL
  ) {
    $sql = "INSERT INTO campaign_activity (
              activity_id,
              campaign_id,
              person_id,
              activity_type_id,
              created_at,
              aws_message_id,
              link_id,
              bounce_type_id
            )
            VALUES (NULL, ?, ?, ?, now(), ?, ?, ?)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('iiisii',
      $campaignId,
      $personId,
      $activityTypeId,
      $awsMessageId,
      $linkId,
      $bounceTypeId
    );
    $stmt->execute();
    $stmt->close();
    return $this->dbh->insert_id;
  }

  public function recordAutoresponderActivity(
    $activityTypeId,
    $autoresponderId,
    $personId,
    $awsMessageId = NULL,
    $linkId = NULL,
    $bounceTypeId = NULL
  ) {
    $sql = "INSERT INTO autoresponder_activity (
              activity_id,
              autoresponder_id,
              person_id,
              activity_type_id,
              created_at,
              aws_message_id,
              link_id,
              bounce_type_id
            )
            VALUES (NULL, ?, ?, ?, now(), ?, ?, ?)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('iiisii',
      $autoresponderId,
      $personId,
      $activityTypeId,
      $awsMessageId,
      $linkId,
      $bounceTypeId
    );
    $stmt->execute();
    $stmt->close();
    return $this->dbh->insert_id;
  }

  public function getSentCampaignActivity($awsMessageId) {
    $sql = "SELECT *
            FROM   campaign_activity
            WHERE  aws_message_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $awsMessageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getSentAutoresponderActivity($awsMessageId) {
    $sql = "SELECT *
            FROM   autoresponder_activity
            WHERE  aws_message_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $awsMessageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function updateStatus($campaignId, $statusId) {
    $sql = "UPDATE campaign
            SET    campaign_status_id = ?,
                   updated_at = now()
            WHERE  campaign_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $statusId, $campaignId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function getPersonById($personId) {
    $sql = "SELECT *
            FROM   person
            WHERE  person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function unsubscribeFromAllLists($personId) {
    $sql = "UPDATE subscriber
            SET    subscriber_status_id = 2,
                   updated_at = now()
            WHERE  person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $stmt->close();
    return $this->dbh->affected_rows;
  }

  public function updateCampaignById($formData) {
    $sql = "UPDATE campaign
            SET    segment_id = ?,
                   from_email_id = ?,
                   subject = ?,
                   body_text = ?,
                   body_html = ?,
                   updated_at = now()
            WHERE  campaign_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('iisssi',
      $formData['segment_id'],
      $formData['from_email_id'],
      $formData['subject'],
      $formData['body_text'],
      $formData['body_html'],
      $formData['campaign_id']
    );
    $stmt->execute();
    $stmt->close();
    return $this->dbh->affected_rows;
  }

  public function saveEmailImage($imageName, $width, $height) {
    $sql = "INSERT INTO email_image (
              image_id,
              image_name,
              image_width,
              image_height,
              created_at
            )
            VALUES (NULL, ?, ?, ?, now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'sii',
      $imageName,
      $width,
      $height
    );
    $stmt->execute();
    $emailImageId = $this->dbh->insert_id;
    $stmt->close();
    return $emailImageId;
  }

  public function getLatestImages() {
    $sql = "SELECT *
            FROM   email_image
            ORDER BY created_at DESC";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function insertAutoresponder($formData) {
    $sql = "INSERT INTO autoresponder (
              autoresponder_id,
              segment_id,
              from_email_id,
              subject,
              body_text,
              body_html,
              duration,
              period,
              delay_in_minutes,
              active,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'iisssisii',
      $formData['segment_id'],
      $formData['from_email_id'],
      $formData['subject'],
      $formData['body_text'],
      $formData['body_html'],
      $formData['duration'],
      $formData['period'],
      $formData['delay_in_minutes'],
      $formData['active']
    );
    $stmt->execute();
    $stmt->close();
    return $this->dbh->insert_id;
  }

  public function getAutoresponderById($autoresponderId) {
    $sql = "SELECT a.*, s.segment_name, fe.email as from_email
	        FROM   autoresponder a
            JOIN   segment s ON s.segment_id = a.segment_id
            JOIN   from_email fe on fe.from_email_id = a.from_email_id
            WHERE  a.autoresponder_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $autoresponderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function updateAutoresponderById($formData) {
    $sql = "UPDATE autoresponder
            SET    segment_id = ?,
                   from_email_id = ?,
                   subject = ?,
                   body_text = ?,
                   body_html = ?,
                   duration = ?,
                   period = ?,
                   delay_in_minutes = ?,
                   active = ?,
                   updated_at = now()
            WHERE  autoresponder_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('iisssisiii',
      $formData['segment_id'],
      $formData['from_email_id'],
      $formData['subject'],
      $formData['body_text'],
      $formData['body_html'],
      $formData['duration'],
      $formData['period'],
      $formData['delay_in_minutes'],
      $formData['active'],
      $formData['autoresponder_id']
    );
    $stmt->execute();
    $stmt->close();
    return $this->dbh->affected_rows;
  }

  public function getAllSegmentsWithAutoresponderCount() {
    $sql = "SELECT s.segment_id, s.segment_name,
                   count(a.autoresponder_id) autoresponder_count
            FROM   segment s
            LEFT JOIN autoresponder a on s.segment_id = a.segment_id
            GROUP BY s.segment_id, s.segment_name";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getAutoresponderStatsBySegment($segmentId) {
    $sql = "SELECT a.autoresponder_id, a.subject, a.active,
                   a.duration, a.period,
                   s.segment_name,
                   sum(case when aa.activity_type_id = 1 then 1 else 0 end) as recipients,
                   count(distinct case when aa.activity_type_id in (2, 5) then aa.person_id else NULL end) as opened,
                   count(distinct case when aa.activity_type_id = 3 then aa.person_id else NULL end) as bounced,
                   count(distinct case when aa.activity_type_id = 4 then aa.person_id else NULL end) as complained,
                   count(distinct case when aa.activity_type_id = 5 then aa.person_id else NULL end) as clicked,
                   count(distinct case when aa.activity_type_id = 6 then aa.person_id else NULL end) as unsubscribed
            FROM   autoresponder a
            JOIN   segment s ON s.segment_id = a.segment_id
            LEFT JOIN   autoresponder_activity aa on aa.autoresponder_id = a.autoresponder_id
            WHERE  a.segment_id = ?
            GROUP BY a.autoresponder_id
            ORDER BY a.delay_in_minutes";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $segmentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getAutoresponderStats($autoresponderId) {
    $sql = "SELECT a.autoresponder_id, a.subject,
                   s.segment_id, s.segment_name,
                   sum(case when aa.activity_type_id = 1 then 1 else 0 end) as recipients,
                   count(distinct case when aa.activity_type_id in (2, 5) then aa.person_id else NULL end) as opened,
                   count(distinct case when aa.activity_type_id = 3 then aa.person_id else NULL end) as bounced,
                   count(distinct case when aa.activity_type_id = 4 then aa.person_id else NULL end) as complained,
                   count(distinct case when aa.activity_type_id = 5 then aa.person_id else NULL end) as clicked,
                   count(distinct case when aa.activity_type_id = 6 then aa.person_id else NULL end) as unsubscribed
            FROM   autoresponder a
            JOIN   segment s ON s.segment_id = a.segment_id
            LEFT JOIN   autoresponder_activity aa on aa.autoresponder_id = a.autoresponder_id
            WHERE  a.autoresponder_id = ?
            GROUP BY a.autoresponder_id";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $autoresponderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getActiveAutoresponders() {
    $sql = "SELECT a.*, s.*, fe.email as from_email
            FROM   autoresponder a
            JOIN   segment s ON s.segment_id = a.segment_id
            JOIN   from_email fe on fe.from_email_id = a.from_email_id
            WHERE  a.active = TRUE
            ORDER BY a.delay_in_minutes";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getAutoresponderSubscribers(
    $delayInMinutes,
    $listId,
    $autoresponderWhereCondition
  ) {
    $sql = "SELECT p.person_id, p.given_name, p.family_name,
                   p.email, p.created_at,
                   s.last_campaign_at, s.last_autoresponder_at
            FROM   subscriber s
            JOIN   person p ON p.person_id = s.person_id
            WHERE  s.list_id = ?
            AND    s.subscriber_status_id = 1
            AND    {$autoresponderWhereCondition}
            AND    s.subscribed_at < now() - INTERVAL ? MINUTE
            AND    s.subscribed_at >= s.last_autoresponder_at - INTERVAL ? MINUTE";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('iii', $listId, $delayInMinutes, $delayInMinutes);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function updateLastAutoresponder($personId, $listId) {
    $sql = "UPDATE subscriber
            SET    last_autoresponder_at = now(),
                   updated_at = now()
            WHERE  person_id = ?
            AND    list_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $personId, $listId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function updateLastCampaign($personId, $listId) {
    $sql = "UPDATE subscriber
            SET    last_campaign_at = now(),
                   updated_at = now()
            WHERE  person_id = ?
            AND    list_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $personId, $listId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }
}
