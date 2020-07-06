<?php
namespace Pyangelo\Repositories;

interface CampaignRepository {

  public function getDraftCampaigns();

  public function getSentCampaigns();

  public function getCampaignStats($campaignId);

  public function getCampaignById($campaignId);

  public function getAllFromEmails();

  public function getAllLists();

  public function getAllSegments();

  public function getListById($listId);

  public function getSegmentById($segmentId);

  public function getFromEmailById($fromEmailId);

  public function insertDraftCampaign($formData);

  public function getCampaignsToBeSent();

  public function getActiveSubscribersFromList($listId);

  public function getSubscribers($listId, $whereCondition);

  public function getSubscriberByPersonId($personId);

  public function getTrackableLink($href);

  public function getTrackableLinkById($linkId);

  public function createTrackableLink($href);

  public function recordCampaignActivity(
    $activityTypeId,
    $campaignId,
    $personId,
    $awsMessageId = NULL,
    $linkId = NULL,
    $bounceTypeId = NULL
  );

  public function recordAutoresponderActivity(
    $activityTypeId,
    $autoresponderId,
    $personId,
    $awsMessageId = NULL,
    $linkId = NULL,
    $bounceTypeId = NULL
  );

  public function getSentCampaignActivity($awsMessageId);

  public function getSentAutoresponderActivity($awsMessageId);

  public function updateStatus($campaignId, $statusId);

  public function getPersonById($personId);

  public function unsubscribeFromAllLists($personId);

  public function updateCampaignById($formData);

  public function saveEmailImage($imageName, $width, $height);

  public function getLatestImages();

  public function insertAutoresponder($formData);

  public function getAutoresponderById($autoresponderId);

  public function updateAutoresponderById($formData);

  public function getAllSegmentsWithAutoresponderCount();

  public function getAutoresponderStatsBySegment($segmentId);

  public function getAutoresponderStats($autoresponderId);

  public function getActiveAutoresponders();

  public function getAutoresponderSubscribers(
    $delayInMinutes,
    $listId,
    $autoresponderWhereCondition
  );

  public function updateLastAutoresponder($personId, $listId);

  public function updateLastCampaign($personId, $listId);
}
