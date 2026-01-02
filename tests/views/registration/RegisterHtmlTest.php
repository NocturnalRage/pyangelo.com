<?php
namespace Tests\views\registration;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class RegisterHtmlTest extends BasicViewHtmlTestCase {

  public function testRegisterPage() {
    $pageTitle = 'PyAngelo - Learn to Program';
    $metaDescription = 'Python Graphics Programming in the Browser';
    $activeLink = 'Home';
    $response = new Response('views');
    $response->setView('registration/register.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $metaDescription,
      'activeLink' => $activeLink,
      'personInfo' => $this->setPersonInfoLoggedOut()
    ));
    $output = $response->requireView();

    $expect = '<h3 class="text-center">Good decision. We\'ll teach you to code.</h3>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<p class="text-center">Let\'s set up your free account. Already have one? <a href="/login">Login</a> now.</p>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<form id="registerForm" method="post" action="/register-validate" class="form-horizontal">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="text" name="givenName" id="givenName" class="form-control" placeholder="First Name" value="" maxlength="100" required autofocus />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="text" name="familyName" id="familyName" class="form-control" placeholder="Last Name" value="" maxlength="100" required />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="email" name="email" id="email" class="form-control" placeholder="Email" value="" maxlength="100" required />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="password" name="loginPassword" id="loginPassword" class="form-control" placeholder="Password" value="" maxlength="30" required />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<button';
    $this->assertStringContainsString($expect, $output);
    $expect = 'class="btn btn-primary"';
    $this->assertStringContainsString($expect, $output);
    $expect = '<i class="fa fa-user-plus" aria-hidden="true"></i> Create My Free Account';
    $this->assertStringContainsString($expect, $output);
    $expect = '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer ></script>';
    $this->assertStringContainsString($expect, $output);
  }
}
?>

