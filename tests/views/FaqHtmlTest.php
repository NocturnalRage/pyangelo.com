<?php
namespace Tests\views;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class FaqHtmlTest extends BasicViewHtmlTest {

  public function testBasicLoginViewWhenLoggedOut() {
    $response = new Response('views');
    $response->setView('faq.html.php');
    $response->setVars(array(
      'loggedIn' => FALSE,
      'pageTitle' => 'FAQ | PyAngelo',
      'metaDescription' => "FAQ of the PyAngelo website.",
      'activeLink' => 'FAQ',
      'personInfo' => $this->setPersonInfoLoggedOut()
    ));
    $output = $response->requireView();
    $expect = 'FAQ | PyAngelo';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
