<?php
namespace Tests\views\admin;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTest;

class UserSearchHtmlTest extends BasicViewHtmlTest {
  public function tearDown(): void {
    \Mockery::close();
  }

  public function testBasicView() {
    $person = [
      'person_id' => 2,
      'given_name' => 'Jeff'
    ];
    $email = 'fastfred@hotmail.com';
    $displayName = 'Fast Fred';
    $people = [
      [
        'person_id' => 100,
        'display_name' => $displayName,
        'country_name' => 'Australia',
        'premium_status_boolean' => 1,
        'email' => 'fastfred@hotmail.com',
        'created_at' => '2017-01-01 12:00:00'
      ]
    ];
    $avatar = \Mockery::mock('Framework\Presentation\Gravatar');
    $avatar->shouldReceive('getAvatarUrl')->once()->with($email)->andReturn('avatar');
    $response = new Response('views');
    $response->setView('admin/user-search.html.php');
    $response->setVars(array(
      'pageTitle' => "CubeSkills Admin",
      'metaDescription' => "Update the CubeSkills website through the administration pages.",
      'activeLink' => 'Admin',
      'personInfo' => $this->setPersonInfoAdmin(),
      'people' => $people,
      'avatar' => $avatar
    ));
    $output = $response->requireView();
    $this->assertStringContainsString($displayName, $output);
    $expected = '<img class="media-object featuredThumbnail" src="avatar" alt="' . $displayName . '" />';
    $this->assertStringContainsString($expected, $output);
  }
}
?>
