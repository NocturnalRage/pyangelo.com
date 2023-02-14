<?php
namespace Tests\views\profile;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class PasswordHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicView() {
    $person = [
      'given_name' => 'Freddy',
      'family_name' => 'Fearless',
      'created_at' => '2016-01-01 10:00:00',
      'memberSince' => '2 weeks ago',
      'country_name' => 'Australia',
      'email' => 'fred@hotmail.com'
    ];
    $response = new Response('views');
    $response->setView('profile/password.html.php');
    $response->setVars(array(
      'pageTitle' => 'Change My Password',
      'metaDescription' => "Change my password.",
      'activeLink' => 'password',
      'personInfo' => $this->setPersonInfoLoggedIn()
    ));
    $output = $response->requireView();
    $expect = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<form id="logout-form" action="/logout" method="POST" style="display: none;">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/password" class="list-group-item active"><i class="fa fa-key fa-fw"></i> Password</a>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h1>Change Your Password</h1>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="password" name="loginPassword" id="loginPassword" class="form-control" placeholder="New password" value="" maxlength="30" required autofocus />';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
