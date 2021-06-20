<?php
namespace Tests\PyAngelo\Repositories;

use PHPUnit\Framework\TestCase;
use PyAngelo\Repositories\MysqlPersonRepository;
use PyAngelo\Repositories\MysqlCountryRepository;
use Tests\Factory\TestData;

class MysqlPersonRepositoryTest extends TestCase {
  protected $dbh;
  protected $personRepository;
  protected $testData;

  public function setUp(): void {
    $dotenv  = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../../../', '.env.test');
    $dotenv->load();
    $this->dbh = new \Mysqli(
      $_ENV['DB_HOST'],
      $_ENV['DB_USERNAME'],
      $_ENV['DB_PASSWORD'],
      $_ENV['DB_DATABASE']
    );
    $this->personRepository = new MysqlPersonRepository($this->dbh);
    $this->testData = new TestData($this->dbh);
  }

  public function tearDown(): void {
    $this->dbh->close();
  }

  public function testInsertRetrieveDeletePerson() {
    $testGivenName = 'Patrick';
    $testFamilyName = 'Dangerfield';
    $testEmail = 'dangerfield@hotmail.com';
    $testPassword = 'password';
    $testCountryCode = 'AU';
    $testDetectedCountryCode = 'AU';

    $updatedGivenName = 'Joel';
    $updatedFamilyName = 'Selwood';
    $updatedEmail = 'joel@geelongfc.com';
    $updatedPassword = 'secret';
    $updatedCountryCode = 'NZ';
    $updatedDetectedCountryCode = 'NZ';

    $membershipActivateToken = 'secret-token';
    $sessionId = 'secret-session';
    $rememberMeToken = 'remember-token';
    $resetToken = 'password-token';

    $this->testData->deleteAllCountries();
    $this->testData->createCountry('AU', 'Australia', 'AUD');
    $this->testData->createCountry('NZ', 'New Zealand', 'NZD');

    $this->testData->deleteAllPeople();

    $personId = $this->personRepository->insertFreeMember(
      $testGivenName,
      $testFamilyName,
      $testEmail,
      $testPassword,
      $testCountryCode,
      $testDetectedCountryCode
    );
    $person = $this->personRepository->getPersonActiveOrNotByEmail($testEmail);
    $this->assertSame($personId, $person['person_id']);
    $this->assertSame($testGivenName, $person['given_name']);
    $this->assertSame($testFamilyName, $person['family_name']);
    $this->assertSame($testEmail, $person['email']);
    $this->assertTrue(password_verify($testPassword, $person['password']));
    $this->assertSame($testCountryCode, $person['country_code']);
    $this->assertSame($testDetectedCountryCode, $person['detected_country_code']);
    $this->assertSame(1, $person['email_status_id']);
    $this->assertSame(0, $person['bounce_count']);
    $this->assertSame(0, $person['active']);

    $person = $this->personRepository->getPersonByEmail($testEmail);
    $this->assertNull($person);

    $rowsUpdated = $this->personRepository->makeActive($personId);
    $this->assertSame(1, $rowsUpdated);
    $person = $this->personRepository->getPersonById($personId);
    $this->assertSame(1, $person['active']);

    $person = $this->personRepository->getPersonById($personId);
    $this->assertSame(1, $person['active']);

    $rowsUpdated = $this->personRepository->updatePerson(
      $personId,
      $updatedGivenName,
      $updatedFamilyName,
      $updatedEmail,
      1,
      $updatedCountryCode,
      $updatedDetectedCountryCode
    );
    $this->assertSame(1, $rowsUpdated);
    $person = $this->personRepository->getPersonByEmail($updatedEmail);
    $this->assertSame($personId, $person['person_id']);
    $this->assertSame($updatedGivenName, $person['given_name']);
    $this->assertSame($updatedFamilyName, $person['family_name']);
    $this->assertSame($updatedEmail, $person['email']);
    $this->assertSame($updatedCountryCode, $person['country_code']);
    $this->assertSame($updatedDetectedCountryCode, $person['detected_country_code']);
    $this->assertSame(1, $person['active']);

    $rowsUpdated = $this->personRepository->incrementBounceCount($personId);
    $person = $this->personRepository->getPersonById($personId);
    $this->assertSame(1, $person['bounce_count']);
    $rowsUpdated = $this->personRepository->incrementBounceCount($personId);
    $person = $this->personRepository->getPersonById($personId);
    $this->assertSame(2, $person['bounce_count']);

    $rowsUpdated = $this->personRepository->setEmailStatus($personId, 2);
    $this->assertSame(1, $rowsUpdated);
    $person = $this->personRepository->getPersonById($personId);
    $this->assertSame(2, $person['email_status_id']);

    $rowsUpdated = $this->personRepository->updatePassword($personId, $updatedPassword);
    $this->assertSame(1, $rowsUpdated);

    $person = $this->personRepository->getPersonById($personId);
    $this->assertTrue(password_verify($updatedPassword, $person['password']));

    $rowsUpdated = $this->personRepository->updateLastLogin($personId);
    $this->assertSame(1, $rowsUpdated);

    $rowsInserted = $this->personRepository->insertMembershipActivate(
      $personId,
      $updatedEmail,
      $membershipActivateToken
    );
    $this->assertSame(1, $rowsInserted);

    $membershipActivate = $this->personRepository->getMembershipActivate(
      $membershipActivateToken
    );
    $this->assertSame($membershipActivateToken, $membershipActivate['token']);

    $rowsUpdated = $this->personRepository->processMembershipActivate(
      $membershipActivateToken
    );
    $this->assertSame(1, $rowsUpdated);
    $membershipActivate = $this->personRepository->getMembershipActivate(
      $membershipActivateToken
    );
    $this->assertNull($membershipActivate);

    $rowsInserted = $this->personRepository->insertRememberMe(
      $personId,
      $sessionId,
      $rememberMeToken
    );
    $this->assertSame(1, $rowsInserted);

    $rememberMe = $this->personRepository->getRememberMe(
      $personId,
      $sessionId
    );
    $this->assertSame($rememberMeToken, $rememberMe['token']);
    $rowsDeleted = $this->personRepository->deleteRememberMe($personId);
    $this->assertSame(1, $rowsDeleted);
    $rememberMe = $this->personRepository->getRememberMe(
      $personId,
      $sessionId
    );
    $this->assertNull($rememberMe);

    $rowsInserted = $this->personRepository->insertPasswordResetRequest(
      $personId,
      $resetToken
    );
    $this->assertSame(1, $rowsInserted);
    $resetRequest = $this->personRepository->getPasswordResetRequest(
      $resetToken
    );
    $this->assertSame($personId, $resetRequest['person_id']);
    $this->assertSame(0, $resetRequest['processed']);

    $rowsUpdated = $this->personRepository->processPasswordResetRequest(
      $personId,
      $resetToken
    );
    $this->assertSame(1, $rowsUpdated);
    $resetRequest = $this->personRepository->getPasswordResetRequest(
      $resetToken
    );
    $this->assertNull($resetRequest);

    $listId = 1;
    $rowsInserted = $this->personRepository->insertSubscriber($listId, $personId);
    $this->assertSame(1, $rowsInserted);
    $subscriber = $this->personRepository->getSubscriber($listId, $personId);
    $this->assertSame(1, $subscriber['subscriber_status_id']);
    $rowsUpdated = $this->personRepository->updateSubscriber(
      $listId,
      $personId,
      4
    );
    $this->assertSame(1, $rowsUpdated);
    $subscriber = $this->personRepository->getSubscriber($listId, $personId);
    $this->assertSame(4, $subscriber['subscriber_status_id']);

    $searchPeople = $this->personRepository->searchByNameAndEmail('Joel Sel');
    $this->assertCount(1, $searchPeople);

    $searchPeople = $this->personRepository->searchByNameAndEmail('Joe Sel geelong');
    $this->assertCount(1, $searchPeople);

    $searchPeople = $this->personRepository->searchByNameAndEmail('Harry Taylor');
    $this->assertCount(0, $searchPeople);
  }

  public function testCreateNotification() {
    $personId = 99;
    $notificationId = $this->createNotification($personId);

    $this->assertGreaterThan(0, $notificationId);

    // Clean up
    $this->testData->deleteAllPeople();
    $this->testData->deleteAllNotifications();
  }
  public function testUnreadCreateNotificationCount() {
    $personId = 99;
    $notificationId = $this->createNotification($personId);

    $unread = $this->personRepository->unreadNotificationCount(
      $personId
    );

    $this->assertEquals(1, $unread['unread']);

    // Clean up
    $this->testData->deleteAllPeople();
    $this->testData->deleteAllNotifications();
  }

  public function testGetUnreadNotifications() {
    $personId = 99;
    $notificationId = $this->createNotification($personId);

    $unread = $this->personRepository->getUnreadNotifications($personId);

    $this->assertEquals($notificationId, $unread[0]['notification_id']);
    $this->assertEquals($personId, $unread[0]['person_id']);
    $this->assertEquals(0, $unread[0]['has_been_read']);

    // Clean up
    $this->testData->deleteAllPeople();
    $this->testData->deleteAllNotifications();
  }

  public function testGetAllNotifications() {
    $personId = 99;
    $unreadNotificationId = $this->createNotification($personId);
    $readNotificationId = $this->personRepository->createNotification(
      $personId,
      2,
      'lesson',
      'Read me'
    );

    $this->personRepository->markNotificationAsRead(
      $personId, $readNotificationId
    );

    $all = $this->personRepository->getAllNotifications($personId);

    $this->assertEquals($unreadNotificationId, $all[0]['notification_id']);
    $this->assertEquals($personId, $all[0]['person_id']);
    $this->assertEquals(0, $all[0]['has_been_read']);
    $this->assertEquals($readNotificationId, $all[1]['notification_id']);
    $this->assertEquals($personId, $all[1]['person_id']);
    $this->assertEquals(1, $all[1]['has_been_read']);
  }

  public function testGetNotificationById() {
    $personId = 99;
    $notificationId = $this->createNotification($personId);

    $notification = $this->personRepository->getNotificationById(
      $personId,
      $notificationId
    );

    $this->assertEquals($notificationId, $notification['notification_id']);
    $this->assertEquals($personId, $notification['person_id']);
  }

  public function testMarkNotificationAsRead() {
    $personId = 99;
    $notificationId = $this->createNotification($personId);

    $unread = $this->personRepository->getUnreadNotifications(
      $personId
    );

    $this->assertEquals($notificationId, $unread[0]['notification_id']);

    $this->personRepository->markNotificationAsRead($personId, $notificationId);

    $unread = $this->personRepository->getUnreadNotifications(
      $personId
    );

    $this->assertEmpty($unread);

    // Clean up
    $this->testData->deleteAllPeople();
    $this->testData->deleteAllNotifications();
  }

  public function testMarkAllNotificationsAsRead() {
    $personId = 99;
    $notificationId = $this->createNotification($personId);

    $unread = $this->personRepository->getUnreadNotifications(
      $personId
    );

    $this->assertEquals($notificationId, $unread[0]['notification_id']);

    $this->personRepository->markAllNotificationsAsRead($personId);

    $unread = $this->personRepository->getUnreadNotifications(
      $personId
    );

    $this->assertEmpty($unread);

    // Clean up
    $this->testData->deleteAllPeople();
    $this->testData->deleteAllNotifications();
  }

  private function createNotification($personId) {
    // Ensure no previous data
    $this->testData->deleteAllPeople();
    $this->testData->deleteAllNotifications();

    // Set up test
    $email = 'fastfred@hotmail.com';
    $notificationTypeId = 1;
    $notificationType = 'blog';
    $data = 'Read me';

    $this->testData->createPerson($personId, $email);

    // Action
    return $this->personRepository->createNotification(
      $personId,
      $notificationTypeId,
      $notificationType,
      $data
    );
  }
}
