<?php
namespace Tests\views\passwordreset;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class ForgotPasswordConfirmHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicForgotPasswordConfirmView() {
    $email = 'any_email@nocturnalrage.com';
    $response = new Response('views');
    $response->setView('password-reset/forgot-password-confirm.html.php');
    $response->setVars(array(
      'pageTitle' => 'Forgot Password',
      'metaDescription' => "I've forgotton my password.",
      'activeLink' => 'Home',
      'email' => $email,
      'personInfo' => $this->setPersonInfoLoggedOut()
    ));
    $output = $response->requireView();
    $expect = '<li><a href="/login">Login</a></li>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<li><a href="/register">Register</a></li>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h1 class="text-center">Password Reset Instruction Emailed</h1>';
    $this->assertStringContainsString($expect, $output);
    $expect = "If a matching account was found then an email was sent to $email with instructions on how to reset your password.";
    $this->assertStringContainsString($expect, $output);
  }
}
?>
