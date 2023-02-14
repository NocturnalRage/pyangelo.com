<?php
namespace Tests\views\profile;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class NotificationsHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicNotificationsView() {
    $notifications = [];
    $response = new Response('views');
    $response->setView('profile/notifications.html.php');
    $response->setVars(array(
      'pageTitle' => 'Notifications',
      'metaDescription' => "Notifications from comments.",
      'activeLink' => 'profile',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'notifications' => $notifications,
      'selection' => 'All'
    ));
    $output = $response->requireView();

    $expect = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">';
    $this->assertStringContainsString($expect, $output);
    $expect = 'Unread Notifications';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
