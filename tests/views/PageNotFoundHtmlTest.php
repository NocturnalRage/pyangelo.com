<?php
namespace Tests\views;

use PHPUnit\Framework\TestCase;
use Framework\Response;

class PageNotFoundHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicViewWhenLoggedOut() {
    $response = new Response('views');
    $response->setView('page-not-found.html.php');
    $response->setVars(array(
      'loggedIn' => FALSE,
      'pageTitle' => 'PyAngelo Page Not Found',
      'metaDescription' => "The page was not found.",
      'activeLink' => 'Home',
      'personInfo' => $this->setPersonInfoLoggedOut()
    ));
    $output = $response->requireView();
    $expect = '<li><a href="/login">Login</a></li>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<li><a href="/register">Register</a></li>';
    $this->assertStringContainsString($expect, $output);
    $expect = "Sorry, the page you are looking for isn't here";
    $this->assertStringContainsString($expect, $output);
  }
}
?>
