<?php
namespace Tests\views\profile;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class NewsletterHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicViewNewsletterPreferencesSubscribed() {
    $person = [
      'given_name' => 'Freddy',
      'family_name' => 'Fearless',
      'created_at' => '2016-01-01 10:00:00',
      'memberSince' => '2 weeks ago',
      'country_name' => 'Australia',
      'email' => 'fred@hotmail.com'
    ];
    $subscribed = true;
    $response = new Response('views');
    $response->setView('profile/newsletter.html.php');
    $response->setVars(array(
      'pageTitle' => 'Update My Newsletter Preferences',
      'metaDescription' => "Update my newsletter preferences.",
      'activeLink' => 'newsletter',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'subscribed' => $subscribed
    ));
    $output = $response->requireView();
    $expect = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<form id="logout-form" action="/logout" method="POST" style="display: none;">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/newsletter" class="list-group-item active"><i class="fa fa-envelope fa-fw"></i> Email Newsletter</a>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h1>PyAngelo Email Newsletter</h1>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="checkbox" name="newsletter" id="newsletter" value="yes" checked />';
    $this->assertStringContainsString($expect, $output);
  }

  public function testBasicViewNewsletterPreferencesUnsubscribed() {
    $person = [
      'given_name' => 'Freddy',
      'family_name' => 'Fearless',
      'created_at' => '2016-01-01 10:00:00',
      'memberSince' => '2 weeks ago',
      'country_name' => 'Australia',
      'email' => 'fred@hotmail.com'
    ];
    $subscribed = false;
    $response = new Response('views');
    $response->setView('profile/newsletter.html.php');
    $response->setVars(array(
      'pageTitle' => 'Update My Newsletter Preferences',
      'metaDescription' => "Update my newsletter preferences.",
      'activeLink' => 'newsletter',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'subscribed' => $subscribed
    ));
    $output = $response->requireView();
    $expect = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<form id="logout-form" action="/logout" method="POST" style="display: none;">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/newsletter" class="list-group-item active"><i class="fa fa-envelope fa-fw"></i> Email Newsletter</a>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h1>PyAngelo Email Newsletter</h1>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="checkbox" name="newsletter" id="newsletter" value="yes" />';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
