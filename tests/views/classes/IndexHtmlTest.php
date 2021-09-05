<?php
namespace Tests\views\classes;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class IndexHtmlTest extends BasicViewHtmlTest {
  public function tearDown(): void {
    \Mockery::close();
  }

  public function testBasicTeacherIndexViewNoClasses() {
    $response = new Response('views');
    $response->setView('classes/index.html.php');
    $response->setVars(array(
      'pageTitle' => 'Teacher Classes',
      'metaDescription' => "Classes I Teach.",
      'activeLink' => 'teacher',
      'personInfo' => $this->setPersonInfoLoggedIn()
    ));
    $output = $response->requireView();
    $expect = '<a href="/classes/teacher" class="list-group-item active"><i class="fa fa-university fa-fw"></i> Teacher</a>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h2>You Have No Classes</h2>';
    $this->assertStringContainsString($expect, $output);
  }

  public function testBasicTeacherIndexViewWithClasses() {
    $classes = [
      [
        'class_id' => 99,
        'class_name' => 'My Great Coding Class',
        'created_at' => '2021-09-10 10:00:00'
      ]
    ];
    $response = new Response('views');
    $response->setView('classes/index.html.php');
    $response->setVars(array(
      'pageTitle' => 'Teacher Classes',
      'metaDescription' => "Classes I Teach.",
      'activeLink' => 'teacher',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'classes' => $classes
    ));
    $output = $response->requireView();
    $expect = '<a href="/classes/teacher" class="list-group-item active"><i class="fa fa-university fa-fw"></i> Teacher</a>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h3><a href="/classes/teacher/99">My Great Coding Class</a></h3>';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
