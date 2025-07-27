<?php
namespace Tests\src\PyAngelo\Auth;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use PyAngelo\Auth\Auth;
use PyAngelo\Repositories\PersonRepository;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class AuthTest extends TestCase {
  protected $request;
  protected $personRepository;
  protected $password;
  protected $passwordHash;
  protected $nonAdminPersonId;
  protected $nonAdminLoginEmail;
  protected $nonAdminPerson;
  protected $adminPersonId;
  protected $adminLoginEmail;
  protected $adminPerson;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->personRepository = Mockery::mock('PyAngelo\Repositories\PersonRepository');

    $this->password = 'secret';
    $this->passwordHash = password_hash($this->password, PASSWORD_DEFAULT);

    $this->nonAdminPersonId = 1000;
    $this->nonAdminLoginEmail = 'any_email@hotmail.com';
    $this->nonAdminPerson = [
      'person_id' => $this->nonAdminPersonId,
      'email' => $this->nonAdminLoginEmail,
      'given_name' => 'Fred',
      'password' => $this->passwordHash,
      'admin' => 0,
      'premium_status_boolean' => 0
    ];

    $this->adminPersonId = 1;
    $this->adminLoginEmail = 'admin@nocturnalrage.com';
    $this->adminPerson = [
      'person_id' => $this->adminPersonId,
      'email' => $this->adminLoginEmail,
      'given_name' => 'Jeff',
      'password' => $this->passwordHash,
      'admin' => 1,
      'premium_status_boolean' => 1
    ];

    session_start();
  }
  public function tearDown(): void {
    Mockery::close();
  }

  #[RunInSeparateProcess]
  public function testUserNotLoggedIn() {
    $auth = new Auth($this->personRepository, $this->request);
    $this->assertFalse($auth->loggedIn());
  }

  #[RunInSeparateProcess]
  public function testUserIsLoggedInWithSessionVariable() {
    $_SESSION['loginEmail'] = $this->nonAdminLoginEmail;
    $this->personRepository->shouldReceive('getPersonByEmail');
    $auth = new Auth($this->personRepository, $this->request);
    $this->assertTrue($auth->loggedIn());
  }

  #[RunInSeparateProcess]
  public function testUserIsLoggedInWithRememberMe() {
    $rememberMeSession = 'a-session';
    $rememberMeToken = 'a-token';
    $rememberMeTokenHash = password_hash($rememberMeToken, PASSWORD_DEFAULT);
    $rememberMeCookie = [
      'person_id' => $this->nonAdminPersonId,
      'session_id' => $rememberMeSession,
      'token' => $rememberMeTokenHash
    ];
    $this->request->cookie['rememberme'] = $this->nonAdminPersonId;
    $this->request->cookie['remembermesession'] = $rememberMeSession;
    $this->request->cookie['remembermetoken'] = $rememberMeToken;
    $this->personRepository->shouldReceive('getPersonByEmail')
      ->once()
      ->with($this->nonAdminLoginEmail)
      ->andReturn($this->nonAdminPerson);
    $this->personRepository->shouldReceive('getRememberMe')
      ->once()
      ->with($this->nonAdminPersonId, $rememberMeSession)
      ->andReturn($rememberMeCookie);
    $this->personRepository->shouldReceive('getPersonById')
      ->once()
      ->with($this->nonAdminPersonId)
      ->andReturn($this->nonAdminPerson);
    $this->personRepository->shouldReceive('updateLastLogin')
      ->once()
      ->with($this->nonAdminPersonId)
      ->andReturn(1);
    $auth = new Auth($this->personRepository, $this->request);
    $this->assertTrue($auth->loggedIn());
    $this->assertSame($this->nonAdminLoginEmail, $_SESSION["loginEmail"]);
    $this->assertSame($this->nonAdminPerson['given_name'], $auth->person()["given_name"]);
  }

  #[RunInSeparateProcess]
  public function testAnonymousUserIsNotAdmin() {
    $auth = new Auth($this->personRepository, $this->request);
    $this->assertFalse($auth->isAdmin());
  }

  #[RunInSeparateProcess]
  public function testAnonymousUserIsNotPremium() {
    $auth = new Auth($this->personRepository, $this->request);
    $this->assertFalse($auth->isPremium());
  }

  #[RunInSeparateProcess]
  public function testLoggedInUserIsNotAdminAndNotPremium() {
    $_SESSION['loginEmail'] = $this->nonAdminLoginEmail;
    $this->personRepository->shouldReceive('getPersonByEmail')
      ->once()
      ->with($_SESSION['loginEmail'])
      ->andReturn($this->nonAdminPerson);
    $auth = new Auth($this->personRepository, $this->request);
    $this->assertFalse($auth->isAdmin());
    $this->assertFalse($auth->isPremium());
  }

  #[RunInSeparateProcess]
  public function testUserIsAdminAndPremiumBecauseOfPremiumStatusBoolean() {
    $_SESSION['loginEmail'] = $this->adminLoginEmail;
    $this->personRepository = Mockery::mock('PyAngelo\Repositories\PersonRepository');
    $this->personRepository->shouldReceive('getPersonByEmail')
      ->once()
      ->with($_SESSION['loginEmail'])
      ->andReturn($this->adminPerson);
    $auth = new Auth($this->personRepository, $this->request);
    $this->assertTrue($auth->isAdmin());
    $this->assertTrue($auth->isPremium());
  }

  #[RunInSeparateProcess]
  public function testPersonWhenNotLoggedIn() {
    $auth = new Auth($this->personRepository, $this->request);

    $person = $auth->person();
    $this->assertNull($person);
  }

  #[RunInSeparateProcess]
  public function testPersonWhenLoggedIn() {
    $_SESSION['loginEmail'] = $this->nonAdminLoginEmail;
    $this->personRepository->shouldReceive('getPersonByEmail')
      ->once()
      ->with($this->nonAdminLoginEmail)
      ->andReturn($this->nonAdminPerson);
    $auth = new Auth($this->personRepository, $this->request);

    $person = $auth->person();
    $this->assertSame($this->nonAdminPerson, $person);
  }

  #[RunInSeparateProcess]
  public function testPersonIdWhenNotLoggedIn() {
    $auth = new Auth($this->personRepository, $this->request);
    $this->assertSame(0, $auth->personId());
  }

  #[RunInSeparateProcess]
  public function testPersonIdWhenLoggedIn() {
    $_SESSION['loginEmail'] = $this->nonAdminLoginEmail;
    $this->personRepository->shouldReceive('getPersonByEmail')
      ->once()
      ->with($this->nonAdminLoginEmail)
      ->andReturn($this->nonAdminPerson);
    $auth = new Auth($this->personRepository, $this->request);
    $this->assertSame($this->nonAdminPersonId, $auth->personId());
  }

  #[RunInSeparateProcess]
  public function testInsertRememberMe() {
    /* Simply test repository function is called */
    $session = 'session';
    $tokenHash = 'token-hash';
    $this->personRepository->shouldReceive('insertRememberMe')
      ->once()
      ->with($this->nonAdminPersonId, $session, $tokenHash)
      ->andReturn(1);
    $auth = new Auth($this->personRepository, $this->request);

    $rowsInserted = $auth->insertRememberMe($this->nonAdminPersonId, $session, $tokenHash);
    $this->assertSame(1, $rowsInserted);
  }

  #[RunInSeparateProcess]
  public function testDeleteRememberMe() {
    $_SESSION['loginEmail'] = $this->nonAdminLoginEmail;
    $this->personRepository->shouldReceive('getPersonByEmail')
      ->once()
      ->with($this->nonAdminLoginEmail)
      ->andReturn($this->nonAdminPerson);
    $this->personRepository->shouldReceive('deleteRememberMe')
      ->once()
      ->with($this->nonAdminPersonId)
      ->andReturn(1);
    $auth = new Auth($this->personRepository, $this->request);

    $rowsDeleted = $auth->deleteRememberMe();
    $this->assertSame(1, $rowsDeleted);
  }

  #[RunInSeparateProcess]
  public function testcreateCrsfToken() {
    $auth = new Auth($this->personRepository, $this->request);

    $crsfToken = $auth->createCrsfToken();
    $this->assertSame($crsfToken, $_SESSION["crsfToken"]);
    $crsfToken2 = $auth->createCrsfToken();
    $this->assertSame($crsfToken2, $crsfToken);
  }

  #[RunInSeparateProcess]
  public function testcreateCrsfTokenIsValid() {
    $auth = new Auth($this->personRepository, $this->request);

    $crsfToken = $auth->createCrsfToken();
    $this->request->post['crsfToken'] = $crsfToken;

    $this->assertTrue($auth->crsfTokenIsValid());
  }

  #[RunInSeparateProcess]
  public function testSetLoginStatusNotLoggedIn() {
    $auth = new Auth($this->personRepository, $this->request);
    $this->assertFalse($auth->setLoginStatus());
  }

  #[RunInSeparateProcess]
  public function testSetLoginStatusLoggedInThroughSessionVariable() {
    $auth = new Auth($this->personRepository, $this->request);
    $_SESSION['loginEmail'] = $this->nonAdminLoginEmail;
    $this->assertTrue($auth->setLoginStatus());
  }

  #[RunInSeparateProcess]
  public function testAuthenticateLoginCorrectPassword() {
    $this->personRepository->shouldReceive('getPersonByEmail')
      ->times(2)
      ->with($this->nonAdminLoginEmail)
      ->andReturn($this->nonAdminPerson);
    $this->personRepository->shouldReceive('updateLastLogin')
      ->once()
      ->with($this->nonAdminPersonId)
      ->andReturn(1);
    $auth = new Auth($this->personRepository, $this->request);

    $authenticated = $auth->authenticateLogin($this->nonAdminLoginEmail, $this->password);
    $this->assertSame(true, $authenticated);
  }

  #[RunInSeparateProcess]
  public function testAuthenticateLoginWrongPassword() {
    $this->personRepository->shouldReceive('getPersonByEmail')
      ->once()
      ->with($this->nonAdminLoginEmail)
      ->andReturn($this->nonAdminPerson);
    $this->personRepository->shouldReceive('updateLastLogin')->never();
    $auth = new Auth($this->personRepository, $this->request);

    $authenticated = $auth->authenticateLogin($this->nonAdminLoginEmail, 'WrongPassword');
    $this->assertFalse($authenticated);
  }

  #[RunInSeparateProcess]
  public function testAuthenticateLoginNoPerson() {
    $email = 'email_does_not_exist_in_database@hotmail.com';
    $auth = new Auth($this->personRepository, $this->request);
    $this->personRepository->shouldReceive('getPersonByEmail')->once()->with($email)->andReturn(NULL);
    $authenticated = $auth->authenticateLogin($email, 'any_password');
    $this->assertFalse($authenticated);
  }

  #[RunInSeparateProcess]
  public function testIsPasswordResetTokenValid() {
    $resetToken = 'password-reset-token';
    $resetRequest = [
      'person_id' => $this->nonAdminPersonId,
      'token' => $resetToken,
      'processed' => 'n'
    ];
    $this->personRepository->shouldReceive('getPasswordResetRequest')
      ->once()
      ->with($resetToken)
      ->andReturn($resetRequest);
    $auth = new Auth($this->personRepository, $this->request);

    $this->assertTrue($auth->isPasswordResetTokenValid($resetToken));
  }

  #[RunInSeparateProcess]
  public function testIsPasswordResetTokenInvalid() {
    $resetToken = 'password-reset-token';
    $invalidResetToken = 'invalid-token';
    $resetRequest = [
      'person_id' => $this->nonAdminPersonId,
      'token' => $resetToken,
      'processed' => 'n'
    ];
    $this->personRepository->shouldReceive('getPasswordResetRequest')
      ->once()
      ->with($invalidResetToken)
      ->andReturn(NULL);
    $auth = new Auth($this->personRepository, $this->request);

    $this->assertFalse($auth->isPasswordResetTokenValid($invalidResetToken));
  }

  #[RunInSeparateProcess]
  public function testImpersonatingFalse() {
    $auth = new Auth($this->personRepository, $this->request);
    $this->assertFalse($auth->impersonating());
  }

  #[RunInSeparateProcess]
  public function testImpersonatingTrue() {
    $_SESSION['impersonator'] = $this->nonAdminLoginEmail;
    $auth = new Auth($this->personRepository, $this->request);
    $this->assertTrue($auth->impersonating());
  }

  #[RunInSeparateProcess]
  public function testGetPersonDetailsForViewsWhenNotLoggedIn() {
    $auth = new Auth($this->personRepository, $this->request);
    $crsfToken = $auth->createCrsfToken();
    $expectedPersonDetails = [
      'loggedIn' => false,
      'details' => NULL,
      'isPremium' => false,
      'isAdmin' => false,
      'isImpersonating' => false,
      'crsfToken' => $crsfToken,
      'unreadNotificationCount' => 0
    ];
    $this->assertSame($expectedPersonDetails, $auth->getPersonDetailsForViews());
  }

  #[RunInSeparateProcess]
  public function testGetPersonDetailsForViewsWhenLoggedIn() {
    $_SESSION['loginEmail'] = $this->nonAdminLoginEmail;
    $this->personRepository
         ->shouldReceive('getPersonByEmail')
         ->once()
         ->with($this->nonAdminLoginEmail)
         ->andReturn($this->nonAdminPerson);

    $unreadNotificationCount = 10;
    $unread = ['unread' => $unreadNotificationCount];
    $this->personRepository
         ->shouldReceive('unreadNotificationCount')
         ->once()
         ->with($this->nonAdminPerson['person_id'])
         ->andReturn($unread);

    $auth = new Auth($this->personRepository, $this->request);
    $crsfToken = $auth->createCrsfToken();
    $expectedPersonDetails = [
      'loggedIn' => true,
      'details' => $this->nonAdminPerson,
      'isPremium' => false,
      'isAdmin' => false,
      'isImpersonating' => false,
      'crsfToken' => $crsfToken,
      'unreadNotificationCount' => $unreadNotificationCount
    ];
    $this->assertEquals($expectedPersonDetails, $auth->getPersonDetailsForViews());
  }

  #[RunInSeparateProcess]
  public function testGetPersonDetailsForViewsWhenLoggedInAsAdmin() {
    $_SESSION['loginEmail'] = $this->adminLoginEmail;
    $this->personRepository
         ->shouldReceive('getPersonByEmail')
         ->once()
         ->with($this->adminLoginEmail)
         ->andReturn($this->adminPerson);

    $unreadNotificationCount = 10;
    $unread = ['unread' => $unreadNotificationCount];
    $this->personRepository
         ->shouldReceive('unreadNotificationCount')
         ->once()
         ->with($this->adminPerson['person_id'])
         ->andReturn($unread);

    $auth = new Auth($this->personRepository, $this->request);
    $crsfToken = $auth->createCrsfToken();
    $expectedPersonDetails = [
      'loggedIn' => true,
      'details' => $this->adminPerson,
      'isPremium' => true,
      'isAdmin' => true,
      'isImpersonating' => false,
      'crsfToken' => $crsfToken,
      'unreadNotificationCount' => $unreadNotificationCount
    ];
    $this->assertSame($expectedPersonDetails, $auth->getPersonDetailsForViews());
  }

  #[RunInSeparateProcess]
  public function testGetPersonDetailsForViewsWhenLoggedInAndImpersonating() {
    $_SESSION['loginEmail'] = $this->adminLoginEmail;
    $_SESSION['impersonator'] = $this->nonAdminLoginEmail;
    $this->personRepository
         ->shouldReceive('getPersonByEmail')
         ->once()
         ->with($this->adminLoginEmail)
         ->andReturn($this->adminPerson);

    $unreadNotificationCount = 10;
    $unread = ['unread' => $unreadNotificationCount];
    $this->personRepository
         ->shouldReceive('unreadNotificationCount')
         ->once()
         ->with($this->adminPerson['person_id'])
         ->andReturn($unread);

    $auth = new Auth($this->personRepository, $this->request);
    $crsfToken = $auth->createCrsfToken();
    $expectedPersonDetails = [
      'loggedIn' => true,
      'details' => $this->adminPerson,
      'isPremium' => true,
      'isAdmin' => true,
      'isImpersonating' => true,
      'crsfToken' => $crsfToken,
      'unreadNotificationCount' => $unreadNotificationCount
    ];
    $this->assertSame($expectedPersonDetails, $auth->getPersonDetailsForViews());
  }

  #[RunInSeparateProcess]
  public function testUnreadNotificationCountWhenNotLoggedIn() {
    $auth = new Auth($this->personRepository, $this->request);
    $this->assertSame(0, $auth->unreadNotificationCount());
  }

  #[RunInSeparateProcess]
  public function testUnreadNotificationCountWhenLoggedIn() {
    $_SESSION['loginEmail'] = $this->nonAdminLoginEmail;
    $this->personRepository->shouldReceive('getPersonByEmail')
      ->once()
      ->with($this->nonAdminLoginEmail)
      ->andReturn($this->nonAdminPerson);

    $unreadNotificationCount = 10;
    $unread = ['unread' => $unreadNotificationCount];
    $this->personRepository
         ->shouldReceive('unreadNotificationCount')
         ->once()
         ->with($this->nonAdminPerson['person_id'])
         ->andReturn($unread);

    $auth = new Auth($this->personRepository, $this->request);

    $this->assertSame($unreadNotificationCount, $auth->unreadNotificationCount());
  }

  #[RunInSeparateProcess]
  public function testCreateNotification() {
    /* Ensure createNotification method is called on personRepository */
    $_SESSION['loginEmail'] = $this->nonAdminLoginEmail;
    $this->personRepository
         ->shouldReceive('getPersonByEmail')
         ->once()
         ->with($this->nonAdminLoginEmail)
         ->andReturn($this->nonAdminPerson);

    $notificationTypeId = 1;
    $notificationType = 2;
    $data = 'some data';
    $this->personRepository
         ->shouldReceive('createNotification')
         ->once()
         ->with($this->nonAdminPersonId, $notificationTypeId, $notificationType, $data)
         ->andReturn($this->nonAdminPerson);

    $auth = new Auth($this->personRepository, $this->request);
    $auth->createNotification(
      $this->nonAdminPersonId,
      $notificationTypeId,
      $notificationType,
      $data
    );

    $this->assertTrue(true);
  }
}
