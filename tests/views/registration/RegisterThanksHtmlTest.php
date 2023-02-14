<?php
namespace Tests\views\registration;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\Views\BasicViewHtmlTestCase;

class RegisterThanksHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicView() {
    $pageTitle = 'Thanks for registering';
    $metaDescription = 'You are now registered';
    $activeLink = 'Home';
    $response = new Response('views');
    $response->setView('registration/thanks-for-registering.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $metaDescription,
      'activeLink' => $activeLink,
      'personInfo' => $this->setPersonInfoLoggedIn()
    ));
    $output = $response->requireView();

    $expect = "Thanks for Joining PyAngelo";
    $this->assertStringContainsString($expect, $output);
  }
}
?>
