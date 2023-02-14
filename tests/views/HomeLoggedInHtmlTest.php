<?php
namespace Tests\views;

use PHPUnit\Framework\TestCase;
use Framework\Response;

class HomeLoggedInHtmlTest extends BasicViewHtmlTestCase {

  public function testPageTitleMetaDescription() {
    $pageTitle = 'PyAngelo - Learn to Program';
    $metaDescription = 'Python Graphics Programming in the Browser';
    $activeLink = 'Home';
    $response = new Response('views');
    $response->setView('home-logged-in.html.php');
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

  public function testNavbarWhenLoggedIn() {
    $pageTitle = 'PyAngelo - Learn to Code';
    $metaDescription = 'Python Graphics Programming in the Browser';
    $activeLink = 'Home';
    $response = new Response('views');
    $response->setView('home-logged-in.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $metaDescription,
      'activeLink' => $activeLink,
      'personInfo' => $this->setPersonInfoLoggedIn()
    ));
    $output = $response->requireView();

    $expect = '<a href="/login">Login</a>';
    $this->assertStringNotContainsString($expect, $output);
    $expect = '<a href="/register">Register</a>';
    $this->assertStringNotContainsString($expect, $output);
    $expect = '<form id="logout-form" action="/logout" method="POST" style="display: none;">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="hidden" name="crsfToken" value="dummy-crsf-token" />';
    $this->assertStringContainsString($expect, $output);
    $expect = 'a href="/admin"><i class="fa fa-lock fa-fw"></i> Admin</a>';
    $this->assertStringNotContainsString($expect, $output);
  }

  public function testNavbarWhenAdmin() {
    $pageTitle = 'PyAngelo - Learn to Code';
    $metaDescription = 'Python Graphics Programming in the Browser';
    $activeLink = 'Home';
    $response = new Response('views');
    $response->setView('home-logged-in.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $metaDescription,
      'activeLink' => $activeLink,
      'personInfo' => $this->setPersonInfoAdmin()
    ));
    $output = $response->requireView();

    $expect = '<a href="/login">Login</a>';
    $this->assertStringNotContainsString($expect, $output);
    $expect = '<a href="/register">Register</a>';
    $this->assertStringNotContainsString($expect, $output);
    $expect = '<form id="logout-form" action="/logout" method="POST" style="display: none;">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="hidden" name="crsfToken" value="dummy-crsf-token" />';
    $this->assertStringContainsString($expect, $output);
    $expect = 'a href="/admin"><i class="fa fa-lock fa-fw"></i> Admin</a>';
    $this->assertStringContainsString($expect, $output);
  }

  public function testNavbarWhenImpersonator() {
    $pageTitle = 'PyAngelo - Learn to Code';
    $metaDescription = 'Python Graphics Programming in the Browser';
    $activeLink = 'Home';
    $response = new Response('views');
    $response->setView('home-logged-in.html.php');
    $response->setVars(array(
      'pageTitle' => $pageTitle,
      'metaDescription' => $metaDescription,
      'activeLink' => $activeLink,
      'personInfo' => $this->setPersonInfoImpersonator()
    ));
    $output = $response->requireView();

    $expect = '<a href="/login">Login</a>';
    $this->assertStringNotContainsString($expect, $output);
    $expect = '<a href="/register">Register</a>';
    $this->assertStringNotContainsString($expect, $output);
    $expect = '<form id="logout-form" action="/logout" method="POST" style="display: none;">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="hidden" name="crsfToken" value="dummy-crsf-token" />';
    $this->assertStringContainsString($expect, $output);
    $expect = 'a href="/admin"><i class="fa fa-lock fa-fw"></i> Admin</a>';
    $this->assertStringContainsString($expect, $output);
    $expect = 'a href="/admin/stop-impersonating"><i class="fa fa-user fa-fw"></i> Stop Impersonating</a>';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
