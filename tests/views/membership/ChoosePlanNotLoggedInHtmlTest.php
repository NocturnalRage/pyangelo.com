<?php
namespace Tests\views\membership;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class ChoosePlanNotLoggedInHtmlTest extends BasicViewHtmlTest {

  public function testBasicLoginViewWhenLoggedOut() {
    $response = new Response('views');
    $response->setView('membership/choose-plan-not-logged-in.html.php');
    $response->setVars(array(
      'pageTitle' => 'Become a PyAngelo Premium Member',
      'metaDescription' => "Log in or create a free account before you sign up to one of our monthly subscription plans.",
      'activeLink' => 'Choose Plan',
      'personInfo' => $this->setPersonInfoLoggedOut(),
    ));
    $output = $response->requireView();
    $expect = '<h3>Do you already have a free PyAngelo account?</h3>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<i class="fa fa-sign-in" aria-hidden="true"></i> Login To Your Account</a>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<i class="fa fa-user-plus" aria-hidden="true"></i> Create Your Free Account</a>';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
