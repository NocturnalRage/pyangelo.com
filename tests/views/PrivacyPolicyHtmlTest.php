<?php
namespace Tests\views;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class PrivacyPolicyHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicLoginViewWhenLoggedOut() {
    $response = new Response('views');
    $response->setView('privacy-policy.html.php');
    $response->setVars(array(
      'loggedIn' => FALSE,
      'pageTitle' => 'Privacy Policy | PyAngelo',
      'metaDescription' => "Privacy Policy of the PyAngelo website.",
      'activeLink' => 'FAQ',
      'personInfo' => $this->setPersonInfoLoggedOut()
    ));
    $output = $response->requireView();
    $expect = 'Privacy Policy | PyAngelo';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
