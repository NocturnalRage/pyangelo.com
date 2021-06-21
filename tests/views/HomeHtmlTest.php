<?php
namespace Tests\views;

use PHPUnit\Framework\TestCase;
use Framework\Response;

class HomeHtmlTest extends BasicViewHtmlTest {

  public function testPageTitleMetaDescription() {
    $pageTitle = 'PyAngelo - Learn to Program';
    $metaDescription = 'Python Graphics Programming in the Browser';
    $activeLink = 'Home';
    $response = new Response('views');
    $response->setView('home.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $metaDescription,
      'activeLink' => $activeLink,
      'personInfo' => $this->setPersonInfoLoggedOut()
    ));
    $output = $response->requireView();

    $expect = '<title>' . $pageTitle . '</title>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<meta name="description" content="' . $metaDescription . '">';
    $this->assertStringContainsString($expect, $output);
  }

  public function testNavbarWhenLoggedOut() {
    $pageTitle = 'PyAngelo - Learn to Program';
    $metaDescription = 'Python Graphics Programming in the Browser';
    $activeLink = 'Home';
    $response = new Response('views');
    $response->setView('home.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $metaDescription,
      'activeLink' => $activeLink,
      'personInfo' => $this->setPersonInfoLoggedOut()
    ));
    $output = $response->requireView();

    $expect = '<a href="/login">Login</a>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/register">Register</a>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<form id="logout-form" action="/logout" method="POST" style="display: none;">';
    $this->assertStringNotContainsString($expect, $output);
    $expect = '<input type="hidden" name="crsfToken" value="dummy-crsf-token" />';
    $this->assertStringNotContainsString($expect, $output);
    $expect = 'a href="/admin"><i class="fa fa-lock fa-fw"></i> Admin</a>';
    $this->assertStringNotContainsString($expect, $output);
  }
}
?>
