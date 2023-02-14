<?php
namespace Tests\views\asktheteacher;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class AskHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicViewAsk() {
    $response = new Response('views');
    $response->setView('ask-the-teacher/ask.html.php');
    $response->setVars(array(
      'pageTitle' => "Ask a question",
      'metaDescription' => "Ask the teacher a question",
      'activeLink' => 'ask-the-teacher',
      'personInfo' => $this->setPersonInfoLoggedIn()
    ));
    $output = $response->requireView();
    $expect = '<h3 class="text-center">Before You Ask</h3>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="text" name="question_title" id="question_title" class="form-control" placeholder="Question Title" value="" maxlength="100" required autofocus />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<textarea name="question" id="question" class="form-control tinymce" placeholder="Enter your question..." rows="10" />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<button type="submit" class="btn btn-primary">';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
