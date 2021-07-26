<?php
namespace PyAngelo\Repositories;

interface PersonRepository {

  public function getPersonActiveOrNotByEmail($email);

  public function getPersonByEmail($email);

  public function getRememberMe($personId, $sessionId);

  public function getPersonById($personId);

  public function unreadNotificationCount($personId);

  public function updatePerson(
    $personId,
    $givenName,
    $familyName,
    $email,
    $active,
    $countryCode,
    $detectedCountryCode
  );

  public function setEmailStatus($personId, $email_status_id);

  public function incrementBounceCount($personId);

  public function makeActive($personId);

  public function insertFreeMember(
    $givenName,
    $familyName,
    $email,
    $loginPassword,
    $countryCode,
    $detectedCountryCode
  );

  public function updatePassword($personId, $password);

  public function insertMembershipActivate($personId, $email, $token);

  public function getMembershipActivate($token);

  public function processMembershipActivate($token);

  public function getPersonByIdForAdmin($personId);

  public function updateLastLogin($personId);

  public function insertRememberMe($personId, $session, $tokenHash);

  public function deleteRememberMe($personId);

  public function insertPasswordResetRequest($personId, $token);

  public function getPasswordResetRequest($token);

  public function processPasswordResetRequest($personId, $token);

  public function insertSubscriber($listId, $personId);

  public function getSubscriber($listId, $personId);

  public function updateSubscriber($listId, $personId, $subscriberStatusId);

  public function getActiveSubscriptionCount($personId);

  public function searchByNameAndEmail($searchTerms);

  public function updatePremiumEndDate($personId, $futureDate);

  public function getPremiumMembers();

  public function getAllNotifications($personId);

  public function getUnreadNotifications($personId);

  public function createNotification(
    $personId,
    $notificationTypeId,
    $notificationType,
    $data
  );

  public function getNotificationById($personId, $notificationId);

  public function markNotificationAsRead($personId, $notificationId);

  public function markAllNotificationsAsRead($personId);
 
  public function getPaymentHistory($personId);
}
?>
