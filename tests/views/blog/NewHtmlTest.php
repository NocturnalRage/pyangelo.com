<?php
namespace Tests\views\blog;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class NewHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicViewNewBlog() {
    $categories = [
      [
        'blog_category_id' => 1,
        'description' => 'Coding Thoughts'
      ],
      [
        'blog_category_id' => 2,
        'description' => 'Coding Tips'
      ]
    ];
    $response = new Response('views');
    $response->setView('blog/new.html.php');
    $response->setVars(array(
      'pageTitle' => "PyAngelo Blog",
      'metaDescription' => "Fun blogs by PyAngelo.",
      'activeLink' => 'blog',
      'personInfo' => $this->setPersonInfoAdmin(),
      'categories' => $categories,
      'submitButtonText' => 'Update'
    ));
    $output = $response->requireView();
    $expect = '<h1 class="text-center">Create a New Blog</h1>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="text" name="title" id="title" class="form-control" placeholder="Title" value="" maxlength="100" required autofocus />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="checkbox" id="featured" name="featured" value="1"  /> Featured Blog Post';
    $this->assertStringContainsString($expect, $output);
    $expect = '<select class="form-control" id="blog_category_id" name="blog_category_id">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<textarea name="content" form="blogForm" id="content" class="form-control tinymce" placeholder="Enter your post..." rows="16" required >';
    $this->assertStringContainsString($expect, $output);
    $expect = '<textarea name="preview" form="blogForm" id="preview" class="form-control tinymce" placeholder="Enter preview text..." rows="3" required >';
    $this->assertStringContainsString($expect, $output);
    $expect = '<input type="submit" class="btn btn-primary" value="Update Blog" />';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
