<?php
namespace Tests\views\asktheteacher;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class CategoryHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicViewCategory() {
    $category = [
      'cagegory_id' => 1,
      'description' => 'Category'
    ];
    $questions = [
      [
        'question_id' => 1,
        'question_title' => 'Question 1',
        'slug' => 'unique-question-slug',
        'updated_at' => '2020-05-01 10:00:00'
      ]
    ];
    $response = new Response('views');
    $response->setView('ask-the-teacher/category.html.php');
    $response->setVars(array(
      'pageTitle' => "Coding Questions",
      'metaDescription' => "Ask the teacher a question",
      'activeLink' => 'ask-the-teacher',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'category' => $category,
      'questions' => $questions
    ));
    $output = $response->requireView();
    $expect = '<h1>Coding Questions on Category</h1>';
    $this->assertStringContainsString($expect, $output);
    $expect = 'Question 1';
    $this->assertStringContainsString($expect, $output);
    $expect = 'unique-question-slug';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
