<?php
namespace Tests\views;

use PHPUnit\Framework\TestCase;
use Framework\Response;

class LoginHtmlTest extends BasicViewHtmlTest {

  public function testBasicLoginViewWhenLoggedOut() {
    $recaptchaKey = 'recaptcha';
    $response = new Response('views');
    $response->setView('login.html.php');
    $response->setVars(array(
      'loggedIn' => FALSE,
      'pageTitle' => 'Login to the PyAngelo Website',
      'metaDescription' => "Login to the PyAngelo website to start coding.",
      'activeLink' => 'Home',
      'personInfo' => $this->setPersonInfoLoggedOut(),
      'recaptchaKey' => $recaptchaKey
    ));
    $output = $response->requireView();
    $expect = '<li><a href="/login">Login</a></li>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<li><a href="/register">Register</a></li>';
    $this->assertStringContainsString($expect, $output);
    $expect = "Welcome Back!";
    $this->assertStringContainsString($expect, $output);
    $expect = '<form id="loginForm" method="post" action="/login-validate" class="form-horizontal">';
    $this->assertStringContainsString($expect, $output);
    $expect = "Login To Your Account";
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/forgot-password">Forgot your password?</a>';
    $this->assertStringContainsString($expect, $output);
    $expect = 'class="g-recaptcha btn btn-primary"';
    $this->assertStringContainsString($expect, $output);
    $expect = 'data-sitekey="' . $recaptchaKey . '"';
    $this->assertStringContainsString($expect, $output);
    $expect = '<script src="https://www.google.com/recaptcha/api.js"></script>';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
