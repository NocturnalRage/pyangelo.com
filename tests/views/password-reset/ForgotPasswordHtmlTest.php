<?php
namespace Tests\views\passwordreset;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class ForgotPasswordHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicLoginViewWhenLoggedOut() {
    $response = new Response('views');
    $response->setView('password-reset/forgot-password.html.php');
    $response->setVars(array(
      'loggedIn' => FALSE,
      'pageTitle' => 'Forgot Your Password',
      'metaDescription' => "If you forgot your password it is no problem",
      'activeLink' => 'Home',
      'personInfo' => $this->setPersonInfoLoggedOut()
    ));
    $output = $response->requireView();
    $expect = '<li><a href="/login">Login</a></li>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<li><a href="/register">Register</a></li>';
    $this->assertStringContainsString($expect, $output);
    $expect = "You've Forgot Your Password";
    $this->assertStringContainsString($expect, $output);
    $expect = '<form method="post" action="/forgot-password-validate" class="form-horizontal">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="hidden" name="crsfToken" value="dummy-crsf-token" />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="submit" class="btn btn-primary" value="Send me a password reset link" />';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
