<?php
namespace Tests\views\tutorials;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class ShowHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicViewWhenNotLoggedIn() {
    $title = 'Tutorial 1';
    $description = 'A great tutorial.';
    $slug = 'tutorial-1';
    $thumbnail = 'tutorial-1.jpg';
    $pdf = 'tutorial-1.pdf';
    $percentComplete = 50;
    $tutorial = [
      'title' => $title,
      'description' => $description,
      'slug' => $slug,
      'thumbnail' => $thumbnail,
      'pdf' => $pdf,
      'level' => 'Beginner',
      'percent_complete' => $percentComplete,
      'lesson_count' => 2,
      'category' => '3x3 Videos',
      'category_slug' => '3x3'
    ];
    $lessons = [
      [
        'lesson_id' => 1,
        'lesson_title' => 'A new lesson',
        'lesson_description' => 'A new lesson to learn from.',
        'lesson_slug' => 'a-new-lesson',
        'lesson_security_level_id' => 1,
        'display_duration' => '1:23',
        'completed' => 1,
        'tutorial_slug' => $slug
      ],
      [
        'lesson_id' => 2,
        'lesson_title' => 'A second lesson',
        'lesson_description' => 'A second lesson to learn from.',
        'lesson_slug' => 'a-second-lesson',
        'lesson_security_level_id' => 2,
        'display_duration' => '1:04',
        'completed' => 0,
        'tutorial_slug' => $slug
      ]
    ];
    $response = new Response('views');
    $response->setView('tutorials/show.html.php');
    $response->setVars(array(
      'pageTitle' => $title,
      'metaDescription' => $description,
      'activeLink' => 'Tutorials',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'tutorial' => $tutorial,
      'lessons' => $lessons
    ));
    $output = $response->requireView();
    $expect = '<img src="/uploads/images/tutorials/' . $thumbnail . '"';
    $this->assertStringContainsString($expect, $output);
    $expect = 'alt="' . $title . '" class="img-responsive featuredThumbnail"';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h4><span class="label label-beginner">Beginner</span></h4>';
    $this->assertStringContainsString($expect, $output);
    $expect = 'Download Tutorial PDF';
    $this->assertStringContainsString($expect, $output);
    $expect = '<i class="fa fa-plus"></i> Create New Lesson</a>';
    $this->assertStringNotContainsString($expect, $output);
    $expect = '<a class="toggleComplete btn btn-success btn1" href="#" data-lesson-id="1" aria-label="Toggle completion">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<strong id="percent-complete">' . $percentComplete . '</strong>% COMPLETE';
    $this->assertStringContainsString($expect, $output);
    $expect = '<div class="table-responsive lessons-table">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<table class="table table-striped table-hover">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a class="toggleComplete btn btn-default btn2" href="#" data-lesson-id="2" aria-label="Toggle completion">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/tutorials/' . $slug . '/' . $lessons[0]['lesson_slug'] . '">';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
