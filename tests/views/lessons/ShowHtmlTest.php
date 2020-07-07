<?php
namespace Tests\views\lessons;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class ShowHtmlTest extends BasicViewHtmlTest {

  public function testBasicViewWhenNotLoggedInUsingAmazon() {
    $tutorial_title = 'Tutorial 1';
    $description = 'A great tutorial.';
    $slug = 'tutorial-1';
    $tutorial_thumbnail = 'tutorial-1.jpg';
    $percentComplete = 50;
    $lesson_title = 'A New Lesson';
    $lesson_description = 'A new lesson to learn from.';
    $tutorial = [
      'title' => $tutorial_title,
      'description' => $description,
      'slug' => $slug,
      'thumbnail' => $tutorial_thumbnail,
      'level' => 'Beginner',
      'percent_complete' => $percentComplete,
      'lesson_count' => 2,
      'category' => 'Coding Videos',
      'category_slug' => 'coding-videos'
    ];
    $lesson = [
      'lesson_id' => 1,
      'lesson_title' => $lesson_title,
      'lesson_description' => $lesson_description,
      'lesson_slug' => 'a-new-lesson',
      'youtube_url' => '',
      'lesson_security_level_id' => 2,
      'display_duration' => '1:23',
      'display_order' => 2,
      'completed' => 1,
      'favourited' => 1,
      'tutorial_id' => 5,
      'tutorial_title' => $tutorial_title,
      'tutorial_thumbnail' => $tutorial_thumbnail,
      'tutorial_slug' => $slug
    ];
    $lessons = [
      $lesson,
      [
        'lesson_id' => 2,
        'lesson_title' => 'A second lesson',
        'lesson_description' => 'A second lesson to learn from.',
        'lesson_slug' => 'a-second-lesson',
        'lesson_security_level_id' => 3,
        'display_duration' => '1:04',
        'completed' => 0,
        'favourited' => 0,
        'tutorial_slug' => $slug
      ]
    ];
    $comments = [];
    $purifier = \Mockery::mock('Framework\Presentation\HtmlPurifierPurify');
    $avatar = \Mockery::mock('Framework\Presentation\Gravatar');
    $response = new Response('views');
    $response->setView('lessons/show.html.php');
    $response->setVars(array(
      'pageTitle' => $tutorial_title,
      'metaDescription' => $description,
      'activeLink' => 'Tutorials',
      'personInfo' => $this->setPersonInfoLoggedOut(),
      'tutorial' => $tutorial,
      'lesson' => $lesson,
      'lessons' => $lessons,
      'signedUrl' => 'video-url',
      'captions' => [],
      'comments' => $comments,
      'alertUser' => false,
      'purifier' => $purifier,
      'avatar' => $avatar,
      'showCommentCount' => 5
    ));
    $output = $response->requireView();
    $expect = '<img src="/uploads/images/tutorials/' . $tutorial_thumbnail . '"';
    $this->assertStringContainsString($expect, $output);
    $expect = '<video id="pyangelo-lesson"';
    $this->assertStringContainsString($expect, $output);
    $expect = "<h1 class=\"text-center\">$lesson_title</h1>";
    $this->assertStringContainsString($expect, $output);
    $expect = "<p class=\"text-center\">$lesson_description</p>";
    $this->assertStringContainsString($expect, $output);
    $expect = 'alt="' . $tutorial_title . '" class="img-responsive featuredThumbnail"';
    $this->assertStringContainsString($expect, $output);
    $expect = '<i class="fa fa-pencil-square-o"></i> Edit Lesson</a>';
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
    $expect = '<a id="favouriteStatus" class="btn btn-block btn-primary" href="#" data-lesson-id="1" aria-label="Toggle favourited">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<i class="fa fa-star" aria-hidden="true"></i>';
    $this->assertStringContainsString($expect, $output);
    $expect = 'Favourite';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/tutorials/' . $slug . '/' . $lessons[0]['lesson_slug'] . '">';
    $this->assertStringContainsString($expect, $output);
  }

  public function testBasicViewWhenNotLoggedInUsingYouTube() {
    $tutorial_title = 'Tutorial 1';
    $description = 'A great tutorial.';
    $slug = 'tutorial-1';
    $tutorial_thumbnail = 'tutorial-1.jpg';
    $percentComplete = 50;
    $lesson_title = 'A New Lesson';
    $lesson_description = 'A new lesson to learn from.';
    $tutorial = [
      'title' => $tutorial_title,
      'description' => $description,
      'slug' => $slug,
      'thumbnail' => $tutorial_thumbnail,
      'level' => 'Beginner',
      'percent_complete' => $percentComplete,
      'lesson_count' => 2,
      'category' => '3x3 Videos',
      'category_slug' => '3x3'
    ];
    $lesson = [
      'lesson_id' => 1,
      'lesson_title' => $lesson_title,
      'lesson_description' => $lesson_description,
      'lesson_slug' => 'a-new-lesson',
      'youtube_url' => 'youtube',
      'lesson_security_level_id' => 2,
      'display_duration' => '1:23',
      'display_order' => 2,
      'completed' => 1,
      'favourited' => 1,
      'tutorial_id' => 5,
      'tutorial_title' => $tutorial_title,
      'tutorial_thumbnail' => $tutorial_thumbnail,
      'tutorial_slug' => $slug
    ];
    $lessons = [
      $lesson,
      [
        'lesson_id' => 2,
        'lesson_title' => 'A second lesson',
        'lesson_description' => 'A second lesson to learn from.',
        'lesson_slug' => 'a-second-lesson',
        'lesson_security_level_id' => 3,
        'display_duration' => '1:04',
        'completed' => 0,
        'favourited' => 0,
        'tutorial_slug' => $slug
      ]
    ];
    $comments = [];
    $purifier = \Mockery::mock('Framework\Presentation\HtmlPurifierPurify');
    $avatar = \Mockery::mock('Framework\Presentation\Gravatar');
    $response = new Response('views');
    $response->setView('lessons/show.html.php');
    $response->setVars(array(
      'pageTitle' => $tutorial_title,
      'metaDescription' => $description,
      'activeLink' => 'Tutorials',
      'personInfo' => $this->setPersonInfoLoggedOut(),
      'tutorial' => $tutorial,
      'lesson' => $lesson,
      'lessons' => $lessons,
      'signedUrl' => 'video-url',
      'captions' => [],
      'comments' => $comments,
      'alertUser' => false,
      'purifier' => $purifier,
      'avatar' => $avatar,
      'showCommentCount' => 5
    ));
    $output = $response->requireView();
    $expect = '<img src="/uploads/images/tutorials/' . $tutorial_thumbnail . '"';
    $this->assertStringContainsString($expect, $output);
    $expect = '<div id="pyangelo-lesson"';
    $this->assertStringContainsString($expect, $output);
    $expect = 'class="embed-responsive embed-responsive-16by9"';
    $this->assertStringContainsString($expect, $output);
    $expect = '<iframe id="pyangelo-video"';
    $this->assertStringContainsString($expect, $output);
    $expect = 'class="embed-responsive-item"';
    $this->assertStringContainsString($expect, $output);
    $expect = 'src="https://www.youtube.com/embed/youtube?enablejsapi=1&rel=0&modestbranding=1&showinfo=0"';
    $this->assertStringContainsString($expect, $output);
    $expect = "<h1 class=\"text-center\">$lesson_title</h1>";
    $this->assertStringContainsString($expect, $output);
    $expect = "<p class=\"text-center\">$lesson_description</p>";
    $this->assertStringContainsString($expect, $output);
    $expect = 'alt="' . $tutorial_title . '" class="img-responsive featuredThumbnail"';
    $this->assertStringContainsString($expect, $output);
    $expect = '<i class="fa fa-pencil-square-o"></i> Edit Lesson</a>';
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
    $expect = '<a id="favouriteStatus" class="btn btn-block btn-primary" href="#" data-lesson-id="1" aria-label="Toggle favourited">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<i class="fa fa-star" aria-hidden="true"></i>';
    $this->assertStringContainsString($expect, $output);
    $expect = 'Favourite';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/tutorials/' . $slug . '/' . $lessons[0]['lesson_slug'] . '">';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
