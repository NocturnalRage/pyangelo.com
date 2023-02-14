<?php
namespace Tests\views\classes;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class NewHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicViewNewClass() {
    $response = new Response('views');
    $response->setView('classes/new.html.php');
    $response->setVars(array(
      'pageTitle' => "Create a new class",
      'metaDescription' => "You can create a new class.",
      'activeLink' => 'teacher',
      'personInfo' => $this->setPersonInfoAdmin(),
      'submitButtonText' => 'Create'
    ));
    $output = $response->requireView();
    $expect = '<h1 class="text-center">Create a New Class</h1>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="text" name="class_name" id="class_name" class="form-control" placeholder="Class name" value="" maxlength="100" required autofocus />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="submit" class="btn btn-primary" value="Create Class" />';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
