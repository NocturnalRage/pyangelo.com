<?php
namespace Tests\src\PyAngelo\Controllers\Lessons;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Lessons\LessonsGetSignedUrlController;

class LessonsGetSignedUrlControllerTest extends TestCase {
  public function tearDown(): void {
    Mockery::close();
  }

  /**
   * @runInSeparateProcess
   */
  public function testInvalidLessonId() {
    session_start();
    $auth = Mockery::mock('PyAngelo\Auth\Auth');
    $tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $cloudfront = Mockery::mock('Framework\CloudFront\CloudFront');
    $request = new Request($GLOBALS);
    $response = new Response('views');

    $controller = new LessonsGetSignedUrlController(
      $request,
      $response,
      $auth,
      $tutorialRepository,
      $cloudfront
    );
    $response = $controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/signed-url.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('No such lesson.', $responseVars['message']);
    $this->assertSame('', $responseVars['signedUrl']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testSignedUrlPremiumLessonPersonNotPremium() {
    session_start();
    $request = new Request($GLOBALS);
    $premiumSecurityId = 3;
    $lessonId = 1;
    $lesson = [
      'lesson_id' => $lessonId,
      'lesson_security_level_id' => $premiumSecurityId
    ];
    $request->post['lessonId'] = $lessonId;
    $auth = Mockery::mock('PyAngelo\Auth\Auth');
    $auth->shouldReceive('isPremium')->once()->with()->andReturn(false);
    $cloudfront = Mockery::mock('Framework\CloudFront\CloudFront');
    $tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $tutorialRepository->shouldReceive('getLessonById')
      ->once()
      ->with($lessonId)
      ->andReturn($lesson);
    $response = new Response('views');

    $controller = new LessonsGetSignedUrlController(
      $request,
      $response,
      $auth,
      $tutorialRepository,
      $cloudfront
    );
    $response = $controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/signed-url.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must be a premium member to view this lesson.', $responseVars['message']);
    $this->assertSame('', $responseVars['signedUrl']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testFreeMemberLessonPersonNotLoggedIn() {
    session_start();
    $request = new Request($GLOBALS);
    $lessonId = 1;
    $freeMemberSecurityId = 2;
    $lesson = [
      'lesson_id' => $lessonId,
      'lesson_security_level_id' => $freeMemberSecurityId
    ];
    $request->post['lessonId'] = $lessonId;
    $auth = Mockery::mock('PyAngelo\Auth\Auth');
    $auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);
    $cloudfront = Mockery::mock('Framework\CloudFront\CloudFront');
    $tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $tutorialRepository->shouldReceive('getLessonById')
      ->once()
      ->with($lessonId)
      ->andReturn($lesson);
    $response = new Response('views');

    $controller = new LessonsGetSignedUrlController(
      $request,
      $response,
      $auth,
      $tutorialRepository,
      $cloudfront
    );
    $response = $controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/signed-url.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must create a free account to view this lesson.', $responseVars['message']);
    $this->assertSame('', $responseVars['signedUrl']);
  }

  /**
   * @runInSeparateProcess
   */
  public function testSignedUrlShowAnyoneVideo() {
    session_start();
    $request = new Request($GLOBALS);
    $anyoneSecurityId = 1;
    $videoName = 'video-lesson-1';
    $signedUrl = 'signed-video-url';
    $youtubeUrl = '';
    $lessonId = 1;
    $lesson = [
      'lesson_id' => $lessonId,
      'video_name' => $videoName,
      'youtube_url' => $youtubeUrl,
      'lesson_security_level_id' => $anyoneSecurityId
    ];
    $request->post['lessonId'] = $lessonId;
    $auth = Mockery::mock('PyAngelo\Auth\Auth');
    $cloudfront = Mockery::mock('Framework\CloudFront\CloudFront');
    $cloudfront->shouldReceive('generateSignedUrl')
      ->once()
      ->andReturn($signedUrl);
    $tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $tutorialRepository->shouldReceive('getLessonById')
      ->once()
      ->with($lessonId)
      ->andReturn($lesson);
    $response = new Response('views');

    $controller = new LessonsGetSignedUrlController(
      $request,
      $response,
      $auth,
      $tutorialRepository,
      $cloudfront
    );
    $response = $controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'lessons/signed-url.json.php';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame('success', $responseVars['status']);
    $this->assertSame('The signed url was successfully generated.', $responseVars['message']);
    $this->assertSame($signedUrl, $responseVars['signedUrl']);
  }
}
?>
