<?php
namespace Tests\views;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class AboutHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicLoginViewWhenLoggedOut() {
    $response = new Response('views');
    $response->setView('about.html.php');
    $response->setVars(array(
      'loggedIn' => FALSE,
      'pageTitle' => 'About PyAngelo',
      'metaDescription' => "All about PyAngelo.",
      'activeLink' => 'About',
      'personInfo' => $this->setPersonInfoLoggedOut()
    ));
    $output = $response->requireView();
    $expect = 'About PyAngelo';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
