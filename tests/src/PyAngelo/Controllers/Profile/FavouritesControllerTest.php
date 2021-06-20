<?php
namespace Tests\src\PyAngelo\Controllers\Profile;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Profile\FavouritesController;

class FavouritesControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->controller = new FavouritesController (
      $this->request,
      $this->response,
      $this->auth,
      $this->tutorialRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Profile\FavouritesController');
  }

  public function testWhenNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(array('header', 'Location: /login'));
    $expectedFlashMessage = "You must be logged in to view your favourites.";
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame($expectedFlashMessage, $this->request->session['flash']['message']);
  }

  public function testWhenLoggedInNoFavourites() {
    $personId = 100;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository->shouldReceive('getAllFavourites')
      ->once()
      ->with($personId)
      ->andReturn(NULL);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/no-favourites.html.php';
    $expectedPageTitle = 'My Favourites';
    $expectedMetaDescription = "Save all your favourite PyAngelo videos here so you can easily find them later.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }

  public function testWhenLoggedInWithFavourites() {
    $personId = 100;
    $favourites = [
      [
        'lesson_id' => 1,
        'tutorial_id' => 10,
        'lesson_title' => 'Great Lesson',
        'lesson_description' => 'A great lesson.',
        'video_name' => 'a-great-lesson.mp4',
        'seconds' => 120,
        'lesson_slug' => 'a-great-lesson'
      ],
      [
        'lesson_id' => 5,
        'tutorial_id' => 11,
        'lesson_title' => 'Great Lesson 2',
        'lesson_description' => 'A great lesson too.',
        'video_name' => 'a-great-lesson-2.mp4',
        'seconds' => 122,
        'lesson_slug' => 'a-great-lesson-2'
      ]
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->auth->shouldReceive('getPersonDetailsForViews')->once()->with();
    $this->tutorialRepository->shouldReceive('getAllFavourites')
      ->once()
      ->with($personId)
      ->andReturn($favourites);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'profile/favourites.html.php';
    $expectedPageTitle = 'My Favourites';
    $expectedMetaDescription = "Save all your favourite PyAngelo videos here so you can easily find them later.";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedPageTitle, $responseVars['pageTitle']);
    $this->assertSame($expectedMetaDescription, $responseVars['metaDescription']);
  }
}
?>
