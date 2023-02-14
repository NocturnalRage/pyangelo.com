<?php
namespace Tests\views\profile;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class ProfileHtmlTest extends BasicViewHtmlTestCase {
  public function tearDown(): void {
    \Mockery::close();
  }

  public function testBasicProfileView() {
    $person = [
      'given_name' => 'Freddy',
      'family_name' => 'Fearless',
      'created_at' => '2016-01-01 10:00:00',
      'memberSince' => '2 weeks ago',
      'country_name' => 'Australia',
      'email' => 'fred@hotmail.com'
    ];
    $points = ['points' => 100];
    $avatarUrl = 'https://myimage.com';
    $avatar = \Mockery::mock('Framework\Contracts\AvatarContract');
    $avatar->shouldReceive('getAvatarUrl')
      ->once()
      ->with($person['email'])
      ->andReturn($avatarUrl);
    $response = new Response('views');
    $response->setView('profile/profile.html.php');
    $response->setVars(array(
      'pageTitle' => 'Profile of Freddy Fearless',
      'metaDescription' => "Your PyAngelo profile.",
      'activeLink' => 'profile',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'person' => $person,
      'points' => $points,
      'avatar' => $avatar
    ));
    $output = $response->requireView();
    $expect = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<form id="logout-form" action="/logout" method="POST" style="display: none;">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/profile" class="list-group-item active"><i class="fa fa-user fa-fw"></i> Profile</a>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<img class="media-object featuredThumbnail" src="https://myimage.com" alt="Freddy Fearless" />';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h1 class="media-heading">Freddy Fearless</h1>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<p>fred@hotmail.com</p>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<p>Joined PyAngelo 2 weeks ago</p>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<p>PyAngelo Points: ' . $points['points'] . '</p>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<p>Australia</p>';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
