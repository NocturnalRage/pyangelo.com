<?php
namespace Tests\views;

use PHPUnit\Framework\TestCase;
use Framework\Response;

abstract class BasicViewHtmlTestCase extends TestCase {

  public function setPersonInfoLoggedOut() {
    return [
      'loggedIn' => false,
      'details' => NULL,
      'isAdmin' => false,
      'isImpersonating' => false,
      'crsfToken' => 'dummy-crsf-token',
      'unreadNotificationCount' => 1
    ];
  }

  public function setPersonInfoLoggedIn($personId = 99) {
    return [
      'loggedIn' => true,
      'details' => [
        'person_id' => $personId,
        'given_name' => 'Fred'
      ],
      'isAdmin' => false,
      'isImpersonating' => false,
      'crsfToken' => 'dummy-crsf-token',
      'unreadNotificationCount' => 1
    ];
  }

  public function setPersonInfoAdmin() {
    return [
      'loggedIn' => true,
      'details' => [
        'person_id' => 99,
        'given_name' => 'Fred'
      ],
      'isAdmin' => true,
      'isImpersonating' => false,
      'crsfToken' => 'dummy-crsf-token',
      'unreadNotificationCount' => 1
    ];
  }

  public function setPersonInfoImpersonator() {
    return [
      'loggedIn' => true,
      'details' => [
        'person_id' => 99,
        'given_name' => 'Fred'
      ],
      'isAdmin' => true,
      'isImpersonating' => true,
      'crsfToken' => 'dummy-crsf-token',
      'unreadNotificationCount' => 1
    ];
  }
}
?>
