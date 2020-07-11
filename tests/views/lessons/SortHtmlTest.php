<?php
namespace Tests\views\lessons;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class SortHtmlTest extends BasicViewHtmlTest {

  public function testBasicViewWhenLoggedIn() {
    $tutorial = [
      'title' => 'Tutorial 1',
      'slug' => 'tutorial-1',
      'display_order' => 1,
      'thumbnail' => 'tutorial-1.jpg'
    ];
    $lessons = [
      [
        'lesson_title' => 'Lesson 1',
        'lesson_slug' => 'lesson-1',
        'display_order' => 1
      ],
      [
        'lesson_title' => 'Lesson 2',
        'lesson_slug' => 'lesson-2',
        'display_order' => 2
      ]
    ];
    $response = new Response('views');
    $response->setView('lessons/sort.html.php');
    $response->setVars(array(
      'pageTitle' => "Sort PyAngelo Lessons",
      'metaDescription' => "Sort the lessons.",
      'activeLink' => 'Tutorials',
      'personInfo' => $this->setPersonInfoAdmin(),
      'tutorial' => $tutorial,
      'lessons' => $lessons,
    ));
    $output = $response->requireView();
    $expect = 'Sort PyAngelo Lessons';
    $this->assertStringContainsString($expect, $output);
    $expect = 'Back to Tutorial 1';
    $this->assertStringContainsString($expect, $output);
    $expect = '<ul id="sortable" class="list-group">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<li id="lesson-1" class=" list-group-item">Lesson 1</li>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<li id="lesson-2" class=" list-group-item">Lesson 2</li>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<script src="https://code.jquery.com/jquery-1.12.4.js"></script>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<script src="/js/notify';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
