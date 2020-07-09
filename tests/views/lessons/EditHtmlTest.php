<?php
namespace Tests\views\lessons;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class EditHtmlTest extends BasicViewHtmlTest {

  public function testBasicViewWhenAdmin() {
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
    $lessonTitle = 'F2L Introduction';
    $lessonDescription = 'We teach you about F2L';
    $seconds = '1:23';
    $videoName = 'f2l.mp4';
    $displayOrder = 10;
    $lessonSlug = 'f2l-introduction';
    $tutorialTitle = 'F2L Magic';
    $tutorialSlug = 'f2l-magic';
    $lesson = [
      'lesson_title' => $lessonTitle,
      'lesson_description' => $lessonDescription,
      'seconds' => $seconds,
      'video_name' => $videoName,
      'display_order' => $displayOrder,
      'lesson_slug' => $lessonSlug,
      'tutorial_title' => $tutorialTitle,
      'tutorial_slug' => $tutorialSlug
    ];
    $formVars = $lesson;
    $response = new Response('views');
    $response->setView('lessons/edit.html.php');
    $response->setVars(array(
      'pageTitle' => "Edit Lesson",
      'metaDescription' => "Edit the $lessonTitle lesson.",
      'activeLink' => 'Tutorials',
      'personInfo' => $this->setPersonInfoAdmin(),
      'securityLevels' => $securityLevels,
      'lesson' => $lesson,
      'formVars' => $formVars,
      'submitButtonText' => 'Update'
    ));
    $output = $response->requireView();
    $expect = 'Edit Lesson';
    $this->assertStringContainsString($expect, $output);
    $expect = 'action="/tutorials/' . $tutorialSlug . '/lessons/' . $lessonSlug . '/update"';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="text" name="lesson_title" id="lesson_title" class="form-control" placeholder="Lesson title" value="' . $lessonTitle . '" maxlength="100" required autofocus />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<textarea name="lesson_description" maxlength="1000" id="lesson_description" class="form-control" placeholder="Enter the description..." rows="8" />' . $lessonDescription . '</textarea>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="text" name="video_name" id="video_name" class="form-control" placeholder="The name of the video including .mp4" value="' . $videoName . '" maxlength="100" required />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="number" min="1" max="9999" name="seconds" id="seconds" class="form-control" placeholder="Duration in seconds" value="' . $seconds . '" required />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<select id="lesson_security_level_id" name="lesson_security_level_id" class="form-control">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<option  value="1">Free members</option>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<option  value="2">Premium members</option>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="number" min="1" max="999" name="display_order" id="display_order" class="form-control" placeholder="Display order" value="' . $displayOrder . '" maxlength="3" required />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="file" name="poster" id="poster" class="form-control" />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="submit" class="btn btn-primary" value="Update Lesson" />';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
