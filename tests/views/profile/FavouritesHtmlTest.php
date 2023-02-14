<?php
namespace Tests\views\profile;

use PHPUnit\Framework\TestCase;
use Framework\Response;
use Tests\views\BasicViewHtmlTestCase;

class FavouritesHtmlTest extends BasicViewHtmlTestCase {

  public function testBasicFavouritesView() {
    $person = [
      'given_name' => 'Freddy',
      'family_name' => 'Fearless',
      'created_at' => '2016-01-01 10:00:00',
      'memberSince' => '2 weeks ago',
      'country_name' => 'Australia',
      'email' => 'fred@hotmail.com'
    ];
    $favourites = [
      [
        'lesson_id' => 1,
        'completed' => 1,
        'lesson_title' => 'Lesson Title',
        'display_duration' => '8:56',
        'lesson_slug' => 'lesson-slug',
        'tutorial_slug' => 'tutorial-slug',
        'tutorial_title' => 'Tutorial Title'
      ]
    ];
    $response = new Response('views');
    $response->setView('profile/favourites.html.php');
    $response->setVars(array(
      'pageTitle' => 'My Favourites',
      'metaDescription' => "My Favourites.",
      'activeLink' => 'favourites',
      'personInfo' => $this->setPersonInfoLoggedIn(),
      'favourites' => $favourites,
    ));
    $output = $response->requireView();
    $expect = '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<form id="logout-form" action="/logout" method="POST" style="display: none;">';
    $this->assertStringContainsString($expect, $output);
    $expect = '<a href="/favourites" class="list-group-item active"><i class="fa fa-star fa-fw"></i> Favourites</a>';
    $this->assertStringContainsString($expect, $output);
    $expect = '<h1>My Favourites</h1>';
    $this->assertStringContainsString($expect, $output);
  }
}
?>
