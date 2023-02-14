<?php
namespace Tests\views\registration;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\Views\BasicViewHtmlTestCase;

class RegisterConfirmHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicView() {
    $pageTitle = 'Confirm Your Email Address';
    $metaDescription = 'Confirm your email address to register';
    $activeLink = 'Home';
    $response = new Response('views');
    $response->setView('registration/please-confirm-your-registration.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $metaDescription,
      'activeLink' => $activeLink,
      'registeredEmail' => 'fastfred@hotmail.com',
      'personInfo' => $this->setPersonInfoLoggedOut()
    ));
    $output = $response->requireView();

    $expect = "You're Almost Done";
    $this->assertStringContainsString($expect, $output);
  }
}
?>
