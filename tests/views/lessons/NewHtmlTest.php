<?php
namespace Tests\views\lessons;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class NewHtmlTest extends BasicViewHtmlTest {

  public function testBasicViewWhenLoggedIn() {
    $securityLevels = [
      [
        'lesson_security_level_id' => 1,
        'description' => 'Free members'
      ],
      [
        'lesson_security_level_id' => 2,
        'description' => 'Premium members'
      ],
    ];
    $tutorialTitle = 'F2L';
    $tutorialSlug = 'f2l';
    $tutorial = ['title' => $tutorialTitle, 'slug' => $tutorialSlug];
    $response = new Response('views');
    $response->setView('lessons/new.html.php');
    $response->setVars(array(
      'pageTitle' => "Create a New Lesson for $tutorialTitle",
      'metaDescription' => "Create a new lesson as part of the $tutorialTitle tutorial.",
      'activeLink' => 'Tutorials',
      'personInfo' => $this->setPersonInfoAdmin(),
      'securityLevels' => $securityLevels,
      'tutorial' => $tutorial,
      'submitButtonText' => 'Create'
    ));
    $output = $response->requireView();
    $expect = 'Create a New Lesson';
    $this->assertStringContainsString($expect, $output);
    $expect = 'action="/tutorials/' . $tutorialSlug . '/lessons/create"';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="text" name="lesson_title" id="lesson_title" class="form-control" placeholder="Lesson title" value="" maxlength="100" required autofocus />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<textarea name="lesson_description" maxlength="1000" id="lesson_description" class="form-control" placeholder="Enter the description..." rows="8" /></textarea>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="text" name="video_name" id="video_name" class="form-control" placeholder="The name of the video including .mp4" value="" maxlength="100" required />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="number" min="1" max="9999" name="seconds" id="seconds" class="form-control" placeholder="Duration in seconds" value="" required />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<select id="lesson_security_level_id" name="lesson_security_level_id" class="form-control">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<option  value="1">Free members</option>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<option  value="2">Premium members</option>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="number" min="1" max="999" name="display_order" id="display_order" class="form-control" placeholder="Display order" value="" maxlength="3" required />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="file" name="poster" id="poster" class="form-control" />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="submit" class="btn btn-primary" value="Create Lesson" />';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
