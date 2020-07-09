<?php
namespace Tests\views\tutorials;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class EditHtmlTest extends BasicViewHtmlTest {

  public function testBasicViewWhenLoggedIn() {
    $categories = [
      [
        'tutorial_category_id' => 1,
        'category' => '3x3 Videos',
        'category_slug' => '3x3'
      ],
      [
        'tutorial_category_id' => 2,
        'category' => '3x3 Algorithms',
        'category_slug' => '3x3-algs'
      ],
    ];
    $levels = [
      [
        'tutorial_level_id' => 1,
        'description' => 'Beginner'
      ],
      [
        'tutorial_level_id' => 2,
        'description' => 'Advanced'
      ],
    ];
    $tutorial = [
      'title' => 'Cross',
      'description' => 'Learn to solve the cross.',
      'slug' => 'cross',
      'display_order' => 1,
      'thumbnail' => 'cross.jpg'
    ];
    $formVars = $tutorial;
    $response = new Response('views');
    $response->setView('tutorials/edit.html.php');
    $response->setVars(array(
      'pageTitle' => "PyAngelo Tutorials",
      'metaDescription' => "Learn how to code.",
      'activeLink' => 'Tutorials',
      'personInfo' => $this->setPersonInfoAdmin(),
      'tutorial' => $tutorial,
      'categories' => $categories,
      'levels' => $levels,
      'formVars' => $formVars,
      'submitButtonText' => 'Update'
    ));
    $output = $response->requireView();
    $expect = 'Edit Cross Tutorial';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="text" name="title" id="title" class="form-control" placeholder="Title" value="Cross" maxlength="100" required autofocus />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<textarea name="description" maxlength="1000" form="tutorialForm" id="description" class="form-control" placeholder="Enter the description..." rows="8" />Learn to solve the cross.</textarea>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<select id="tutorial_level_id" name="tutorial_level_id" class="form-control">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<option  value="1">Beginner</option>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="number" min="1" max="999" name="display_order" id="display_order" class="form-control" placeholder="Display order" value="1" maxlength="3" required />';
    $this->assertStringContainsString($expect, $output);
    $expect = 'name="thumbnail"';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="file" name="pdf" id="pdf" class="form-control" />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="submit" class="btn btn-primary" value="Update Tutorial" />';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
