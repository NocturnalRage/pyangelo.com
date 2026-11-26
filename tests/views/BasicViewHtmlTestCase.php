<?php
namespace Tests\views;

use PHPUnit\Framework\TestCase;
use Framework\Response;

abstract class BasicViewHtmlTestCase extends TestCase {

  protected function setUp(): void {
    parent::setUp();
    // The login, register, forgot password and contact forms show the
    // Turnstile widget. Without this, whether TURNSTILE_SITE_KEY is set
    // depends on which tests ran first. This is Cloudflare's public
    // always-pass test site key, as in .env.example.
    $_ENV['TURNSTILE_SITE_KEY'] ??= '1x00000000000000000000AA';
  }

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
