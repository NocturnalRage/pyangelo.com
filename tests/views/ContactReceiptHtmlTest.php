<?php
namespace Tests\views;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class ContactReceiptHtmlTest extends BasicViewHtmlTestCase {

  public function testPageTitleMetaDescription() {
    $pageTitle = 'PyAngelo - Thanks for contacting us.';
    $metaDescription = 'Thanks for getting in touch';
    $activeLink = 'Home';
    $response = new Response('views');
    $response->setView('contact-receipt.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $metaDescription,
      'activeLink' => $activeLink,
      'personInfo' => $this->setPersonInfoLoggedOut()
    ));
    $output = $response->requireView();

    $expect = 'Thanks for getting in touch with us';
    $this->assertStringContainsString($expect, $output);
    $expect = '<img src="/images/jeff-plumb.jpg" class="img-responsive featuredThumbnail" alt="Thanks for contacting PyAngelo">';
    $this->assertStringContainsString($expect, $output);
  }
}
?>

