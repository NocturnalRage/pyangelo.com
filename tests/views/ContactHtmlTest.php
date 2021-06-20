<?php
namespace Tests\views;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class ContactHtmlTest extends BasicViewHtmlTest {

  public function testPageTitleMetaDescription() {
    $pageTitle = 'PyAngelo - Contact Us';
    $metaDescription = 'Contact the team at PyAngelo';
    $activeLink = 'Home';
    $response = new Response('views');
    $response->setView('contact.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $metaDescription,
      'activeLink' => $activeLink,
      'personInfo' => $this->setPersonInfoLoggedOut()
    ));
    $output = $response->requireView();

    $expect = 'How Can We Help?';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="text" name="name" id="name" class="form-control" placeholder="Name" value="" maxlength="100" required autofocus />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="email" name="email" id="email" class="form-control" placeholder="Email" value="" maxlength="100" required />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="email" name="email" id="email" class="form-control" placeholder="Email" value="" maxlength="100" required />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<textarea name="inquiry" maxlength="1000" id="inquiry" class="form-control" placeholder="What\'s on your mind?" rows="8" /></textarea>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<button';
    $this->assertStringContainsString($expect, $output);
    $expect = 'class="g-recaptcha btn btn-primary"';
    $this->assertStringContainsString($expect, $output);
    $expect = '<i class="fa fa-envelope" aria-hidden="true"></i> Contact Us';
    $this->assertStringContainsString($expect, $output);
    $expect = '<script src="https://www.google.com/recaptcha/api.js"></script>';
    $this->assertStringContainsString($expect, $output);
  }
}
?>

