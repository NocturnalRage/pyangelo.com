<?php
namespace Tests\views;

use PHPUnit\Framework\TestCase;
use Framework\Response;

abstract class BasicViewHtmlTest extends TestCase {

  public function setPersonInfoLoggedOut() {
    return [
      'loggedIn' => false,
      'details' => NULL,
      'isAdmin' => false,
      'isImpersonating' => false,
      'crsfToken' => 'dummy-crsf-token'
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
      'crsfToken' => 'dummy-crsf-token'
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
      'crsfToken' => 'dummy-crsf-token'
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
      'crsfToken' => 'dummy-crsf-token'
    ];
  }
}
?>
