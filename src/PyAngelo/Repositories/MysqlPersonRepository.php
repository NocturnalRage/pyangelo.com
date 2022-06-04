<?php
namespace PyAngelo\Repositories;

class MysqlPersonRepository implements PersonRepository {
  protected $dbh;

  public function __construct(\Mysqli $dbh) {
    $this->dbh = $dbh;
  }

  public function getPersonActiveOrNotByEmail($email) {
    $sql = "SELECT *
	        FROM   person
            WHERE  email = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getPersonByEmail($email) {
    $sql = "SELECT p.*,
                   c.country_name,
                   CASE WHEN p.premium_end_date > now()
                        THEN 1
                        ELSE 0
                   END as premium_status_boolean
	        FROM   person p
            JOIN   country c on c.country_code = p.country_code
            WHERE  email = ?
            AND    active = 1";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getRememberMe($personId, $sessionId) {
    $sql = "SELECT *
            FROM   remember_me
            WHERE  person_id = ?
            AND    session_id = ?
            AND    expires_at > now()";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('is', $personId, $sessionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getPersonById($personId) {
    $sql = "SELECT p.*,
                   CASE WHEN p.premium_end_date > now()
                        THEN 1
                        ELSE 0
                   END as premium_status_boolean
            FROM   person p
            WHERE  active = 1
            AND    person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function unreadNotificationCount($personId) {
    $sql = "SELECT count(*) as unread
            FROM   notification
            WHERE  person_id = ?
            AND    has_been_read = FALSE";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function updatePerson(
    $personId,
    $givenName,
    $familyName,
    $email,
    $active,
    $countryCode,
    $detectedCountryCode
  ) {
    $sql = "UPDATE person
            SET    given_name = ?,
                   family_name = ?,
                   email = ?,
                   active = ?,
                   country_code = ?,
                   detected_country_code = ?,
                   updated_at = now()
            WHERE  person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'sssissi',
      $givenName,
      $familyName,
      $email,
      $active,
      $countryCode,
      $detectedCountryCode,
      $personId
    );
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function setEmailStatus($personId, $email_status_id) {
    $sql = "UPDATE person
            SET    email_status_id = ?
            WHERE  person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $email_status_id, $personId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function incrementBounceCount($personId) {
    $sql = "UPDATE person
            SET    bounce_count = bounce_count + 1
            WHERE  person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function makeActive($personId) {
    $sql = "UPDATE person
            SET    active = 1,
                   updated_at = now()
            WHERE  person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function insertFreeMember(
    $givenName,
    $familyName,
    $email,
    $loginPassword,
    $countryCode,
    $detectedCountryCode
  ) {
    $hashedPassword = password_hash($loginPassword, PASSWORD_DEFAULT);
    $sql = "INSERT INTO person (
              person_id,
              given_name,
              family_name,
              email,
              password,
              email_status_id,
              bounce_count,
              active,
              country_code,
              detected_country_code,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, ?, ?, 1, 0, 0, ?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'ssssss',
      $givenName,
      $familyName,
      $email,
      $hashedPassword,
      $countryCode,
      $detectedCountryCode
    );
    $stmt->execute();
    $stmt->close();
    return $this->dbh->insert_id;
  }

  public function updatePassword($personId, $password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE person
            SET    password = ?,
                   updated_at = now()
            WHERE  person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'si',
      $hashedPassword,
      $personId
    );
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function insertMembershipActivate($personId, $email, $token) {
    $sql = "INSERT INTO membership_activate (
              person_id,
              email,
              token,
              processed,
              created_at,
              processed_at
            )
            VALUES (?, ?, ?, 0, now(), NULL)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'iss',
      $personId,
      $email,
      $token
    );
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function getMembershipActivate($token) {
    $sql = "SELECT *
            FROM   membership_activate
            WHERE  token = ?
            AND    processed = 0";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function processMembershipActivate($token) {
    $sql = "update membership_activate
            set    processed = 1,
                   processed_at = now()
            where  token = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function getPersonByIdForAdmin($personId) {
    $sql = "SELECT p.*,
                   concat(p.given_name, ' ', p.family_name) as display_name,
                   c.country_name,
                   CASE WHEN p.premium_end_date > now()
                        THEN 1
                        ELSE 0
                   END as premium_status_boolean
            FROM   person p
            JOIN   country c on c.country_code = p.country_code
            WHERE  active = 1
            AND    person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function updateLastLogin($personId) {
    $sql = "update person set last_login = now() where person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function insertRememberMe($personId, $session, $tokenHash) {
    $sql = "insert into remember_me
            (person_id, session_id, token, created_at, expires_at)
            values
            (?, ?, ?, now(), date_add(now(), INTERVAL 12 MONTH))";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('iss', $personId, $session, $tokenHash);
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function deleteRememberMe($personId) {
    $sql = "DELETE FROM remember_me
            WHERE  person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $rowsDeleted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsDeleted;
  }

  public function insertPasswordResetRequest($personId, $token) {
    $sql = "insert into password_reset_request
            (person_id, token, processed, created_at, processed_at)
            values
            (?, ?, 0, now(), NULL)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('is', $personId, $token);
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function getPasswordResetRequest($token) {
    $sql = "SELECT person_id, processed, created_at
            FROM   password_reset_request
            WHERE  token = ?
            AND    processed = 0
            AND    created_at > date_sub(now(), INTERVAL 24 HOUR)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function processPasswordResetRequest($personId, $token) {
    $sql = "update password_reset_request
            set    processed = 1,
                   processed_at = now()
            where  person_id = ?
            and    token = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('is', $personId, $token);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function insertSubscriber($listId, $personId) {
    $sql = "INSERT INTO subscriber (
              list_id,
              person_id,
              subscriber_status_id,
              created_at,
              updated_at,
              subscribed_at,
              last_campaign_at,
              last_autoresponder_at
            )
            VALUES (?, ?, 1, now(), now(), now(), now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $listId, $personId);
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function getSubscriber($listId, $personId) {
    $sql = "SELECT *
            FROM   subscriber
            WHERE  list_id = ?
            AND    person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $listId, $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function updateSubscriber($listId, $personId, $subscriberStatusId) {
    $sql = "UPDATE subscriber
            SET    subscriber_status_id = ?,
                   updated_at = now()
            WHERE  list_id = ?
            AND    person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('iii', $subscriberStatusId, $listId, $personId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function getActiveSubscriptionCount($personId) {
    $sql = "SELECT count(*) as active_subscription_count
	          FROM   stripe_subscription
            WHERE  person_id = ?
            AND    status in ('active', 'past_due')";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function searchByNameAndEmail($searchTerms) {
    $sql = "SELECT p.person_id,
                   concat(p.given_name, ' ', p.family_name) as display_name,
                   p.email,
                   c.country_name,
                   CASE WHEN p.premium_end_date > now()
                        THEN 1
                        ELSE 0
                   END as premium_status_boolean,
                   p.premium_end_date,
                   p.created_at
	          FROM   person p
            JOIN   country c on c.country_code = p.country_code
            WHERE  active = 1 ";
    $terms = explode(" ", $searchTerms);
    $paramTypes = '';
    $likeParams = [];
    foreach ($terms as $term) {
      $sql .= "AND (email LIKE ? OR given_name LIKE ? OR family_name LIKE ?)";
      $paramTypes .= 'sss';
      $likeParams[] = '%' . $term . '%';
      $likeParams[] = '%' . $term . '%';
      $likeParams[] = '%' . $term . '%';
    }
    $params[] = $paramTypes;
    for ($i = 0; $i < count($likeParams); ++$i) {
      $params[] = &$likeParams[$i];
    }

    $stmt = $this->dbh->prepare($sql);
    call_user_func_array(array($stmt, 'bind_param'), $params);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function updatePremiumEndDate($personId, $futureDate) {
    $sql = "UPDATE person
            SET    premium_end_date = ?,
                   updated_at = now()
            WHERE  person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('si', $futureDate, $personId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function getPremiumMembers() {
    $sql = "SELECT p.person_id,
                   concat(p.given_name, ' ', p.family_name) as display_name,
                   p.email,
                   c.country_name,
                   CASE WHEN p.premium_end_date > now()
                        THEN 1
                        ELSE 0
                   END as premium_status_boolean,
                   p.created_at,
                   max(ss.start_date) premium_start_date,
                   p.premium_end_date
            FROM   person p
            JOIN   country c on c.country_code = p.country_code
            LEFT JOIN stripe_subscription ss on ss.person_id = p.person_id
            WHERE  p.premium_end_date > now()
            GROUP BY p.person_id
            ORDER by max(ss.start_date) DESC";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getUnreadNotifications($personId) {
    $sql = "SELECT *
            FROM   notification
            WHERE  person_id = ?
            AND    has_been_read = FALSE
            ORDER BY created_at DESC";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getAllNotifications($personId) {
    $sql = "SELECT *
            FROM   notification
            WHERE  person_id = ?
            ORDER BY created_at DESC";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function createNotification(
    $personId,
    $notificationTypeId,
    $notificationType,
    $data
  ) {
    $sql = "INSERT INTO notification (
              notification_id,
              notification_type_id,
              notification_type,
              person_id,
              data,
              has_been_read,
              read_at,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, ?, ?, 0, NULL, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'isis',
      $notificationTypeId,
      $notificationType,
      $personId,
      $data
    );
    $stmt->execute();
    $stmt->close();
    return $this->dbh->insert_id;
  }

  public function getNotificationById($personId, $notificationId) {
    $sql = "SELECT *
	        FROM   notification
            WHERE  notification_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $notificationId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function markNotificationAsRead($personId, $notificationId) {
    $sql = "UPDATE notification
            SET    has_been_read = TRUE,
                   read_at = now()
            WHERE  person_id = ?
            AND    notification_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $personId, $notificationId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function markAllNotificationsAsRead($personId) {
    $sql = "UPDATE notification
            SET    has_been_read = TRUE,
                   read_at = now()
            WHERE  person_id = ?
            AND    has_been_read = FALSE";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function getPaymentHistory($personId) {
    $sql = "SELECT ssp.subscription_id,
                   ssp.payment_type_id,
                   ssp.currency_code,
                   ssp.total_amount_in_cents,
                   ssp.charge_id,
                   ssp.original_charge_id,
                   ssp.refund_status,
                   ssp.total_amount_in_cents / c.stripe_divisor display_amount,
                   DATE_FORMAT(ssp.paid_at, '%W %D %M %Y') paid_at_formatted,
                   pt.payment_type_name,
                   c.currency_symbol
            FROM   stripe_subscription ss
            JOIN   stripe_subscription_payment ssp on ss.subscription_id = ssp.subscription_id
            JOIN   stripe_payment_type pt on ssp.payment_type_id = pt.payment_type_id
            JOIN   currency c on ssp.currency_code = c.currency_code
            WHERE  ss.person_id = ?
            ORDER BY ssp.paid_at DESC";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getPoints($personId) {
    $sql = "SELECT sum(ml.points) points
            FROM   skill_mastery sm
            JOIN   mastery_level ml on sm.mastery_level_id = ml.mastery_level_id
            WHERE  sm.person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }
}
?>
