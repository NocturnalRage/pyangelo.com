<?php
namespace Tests\views\passwordreset;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class ResetPasswordHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicResetPasswordView() {
    $token = 'secret';
    $response = new Response('views');
    $response->setView('password-reset/reset-password.html.php');
    $response->setVars(array(
      'pageTitle' => 'Reset Password',
      'metaDescription' => "Enter a new password.",
      'activeLink' => 'Home',
      'personInfo' => $this->setPersonInfoLoggedOut(),
      'token' => $token
    ));
    $output = $response->requireView();
    $expect = '<li><a href="/login">Login</a></li>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<li><a href="/register">Register</a></li>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h1 class="text-center">You\'re Nearly Done</h1>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="password" name="loginPassword" id="loginPassword" class="form-control" placeholder="New password" value="" maxlength="30" required autofocus />';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
