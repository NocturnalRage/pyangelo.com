<?php
namespace tests\src\PyAngelo\FormServices;

use Mockery;
use PHPUnit\Framework\TestCase;
use PyAngelo\FormServices\LessonFormService;

class LessonFormServiceTest extends TestCase {
  public function setUp(): void {
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->lessonFormService = new LessonFormService($this->tutorialRepository);
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testCreateLessonWithNoFormData() {
    $formData = [];

    $success = $this->lessonFormService->createLesson($formData);
    $flashMessage = $this->lessonFormService->getFlashMessage();
    $errors = $this->lessonFormService->getErrors();
    $expectedFlashMessage = 'There were some errors. Please fix these and then we will create the lesson.';
    $expectedErrors = [
      'display_order' => 'You must select where this will be displayed relative to other lessons in this tutorial series.',
      'tutorial_id' => 'This lesson must be part of a tutorial series.',
      'lesson_title' => 'You must supply a title for this lesson.',
      'lesson_description' => 'The lesson description cannot be blank.',
      'video_name' => 'You must supply a video name.',
      'seconds' => 'You must record the duration of this lesson in seconds.',
      'lesson_security_level_id' => 'You must select the security level for this lesson.'
    ];
    $this->assertFalse($success);
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testCreateLessonWithDataTooLong() {
    $securityLevelId = 1;
    $securityLevel = ['lesson_security_level_id' => 1, 'description' => 'Free'];
    $displayOrder = 1;
    $videoName = 'long-lesson.mp4';
    $seconds = 120;
    $tutorialTitle = 'Tutorial';
    $tutorialSlug = 'tutorial-slug';
    $tutorial = [
      'tutorial_id' => 1,
      'tutorial_title' => $tutorialTitle
    ];
    $this->tutorialRepository->shouldReceive('getLessonSecurityLevelById')
      ->once()
      ->with($securityLevelId)
      ->andReturn($securityLevel);
    $this->tutorialRepository->shouldReceive('getTutorialBySlug')
      ->once()
      ->with($tutorialSlug)
      ->andReturn($tutorial);
    $formData = [
      'lesson_title' => 'A very very very very long title that is much more than 100 characater long and will be rejected by the validation which expects the title to be less than 100 characters',
      'slug' => $tutorialSlug,
      'video_name' => $videoName,
      'seconds' => $seconds,
      'lesson_security_level_id' => $securityLevelId,
      'lesson_description' => 'So short.',
      'lesson_security_level_id' => $securityLevelId,
      'display_order' => $displayOrder
    ];

    $success = $this->lessonFormService->createLesson($formData);
    $flashMessage = $this->lessonFormService->getFlashMessage();
    $errors = $this->lessonFormService->getErrors();
    $expectedFlashMessage = 'There were some errors. Please fix these and then we will create the lesson.';
    $expectedErrors = [
      'lesson_title' => 'The title of this lesson can be no longer than 100 characters.'
    ];
    $this->assertFalse($success);
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testCreateLessonWithInvalidLevel() {
    $lessonTitle = 'A Good Lesson';
    $lessonSlug = 'a-good-lesson';
    $securityLevelId = 5;
    $displayOrder = 1;
    $videoName = 'long-lesson.mp4';
    $seconds = 120;
    $tutorialTitle = 'Tutorial';
    $tutorialSlug = 'tutorial-slug';
    $tutorial = [
      'tutorial_id' => 1,
      'tutorial_title' => $tutorialTitle
    ];
    $this->tutorialRepository->shouldReceive('getLessonByTitleAndTutorialId')
      ->once()
      ->with($lessonTitle, $tutorial['tutorial_id'])
      ->andReturn(NULL);
    $this->tutorialRepository->shouldReceive('getLessonSecurityLevelById')
      ->once()
      ->with($securityLevelId)
      ->andReturn(NULL);
    $this->tutorialRepository->shouldReceive('getTutorialBySlug')
      ->once()
      ->with($tutorialSlug)
      ->andReturn($tutorial);
    $formData = [
      'lesson_title' => $lessonTitle,
      'slug' => $tutorialSlug,
      'video_name' => $videoName,
      'seconds' => $seconds,
      'lesson_security_level_id' => $securityLevelId,
      'lesson_description' => 'So short.',
      'lesson_security_level_id' => $securityLevelId,
      'display_order' => $displayOrder
    ];

    $success = $this->lessonFormService->createLesson($formData);
    $flashMessage = $this->lessonFormService->getFlashMessage();
    $errors = $this->lessonFormService->getErrors();
    $expectedFlashMessage = 'There were some errors. Please fix these and then we will create the lesson.';
    $expectedErrors = [
      'lesson_security_level_id' => 'The specified security level for this lesson does not exist.'
    ];
    $this->assertFalse($success);
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testCreateLessonWithValidData() {
    $lessonTitle = 'A Good Lesson';
    $securityLevelId = 1;
    $securityLevel = ['lesson_security_level_id' => 1, 'description' => 'Free'];
    $displayOrder = 1;
    $videoName = 'long-lesson.mp4';
    $seconds = 120;
    $lessonSlug = 'a-good-lesson';
    $tutorialTitle = 'Tutorial';
    $tutorialSlug = 'tutorial-slug';
    $tutorialId = 1;
    $tutorial = [
      'tutorial_id' => $tutorialId,
      'tutorial_title' => $tutorialTitle
    ];
    $formData = [
      'lesson_title' => $lessonTitle,
      'slug' => $tutorialSlug,
      'video_name' => $videoName,
      'seconds' => $seconds,
      'lesson_security_level_id' => $securityLevelId,
      'lesson_description' => 'So short.',
      'lesson_security_level_id' => $securityLevelId,
      'display_order' => $displayOrder,
      'lesson_slug' => $lessonSlug,
      'tutorial_id' => $tutorialId
    ];
    $this->tutorialRepository->shouldReceive('getLessonByTitleAndTutorialId')
      ->once()
      ->with($lessonTitle, $tutorial['tutorial_id'])
      ->andReturn(NULL);
    $this->tutorialRepository->shouldReceive('getLessonSecurityLevelById')
      ->once()
      ->with($securityLevelId)
      ->andReturn($securityLevel);
    $this->tutorialRepository->shouldReceive('getTutorialBySlug')
      ->once()
      ->with($tutorialSlug)
      ->andReturn($tutorial);
    $this->tutorialRepository->shouldReceive('getLessonBySlugAndTutorialId')
      ->once()
      ->with($lessonSlug, $tutorialId)
      ->andReturn(NULL);
    $this->tutorialRepository->shouldReceive('insertLesson')
      ->once()
      ->with($formData)
      ->andReturn(4);

    $success = $this->lessonFormService->createLesson($formData);
    $flashMessage = $this->lessonFormService->getFlashMessage();
    $errors = $this->lessonFormService->getErrors();
    $expectedErrors = [];
    $this->assertTrue($success);
    $this->assertNull($flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testCreateLessonWithValidDataAndExistingSlug() {
    $lessonTitle = 'A Good Lesson';
    $securityLevelId = 1;
    $securityLevel = ['lesson_security_level_id' => 1, 'description' => 'Free'];
    $displayOrder = 1;
    $videoName = 'long-lesson.mp4';
    $seconds = 120;
    $lessonSlug = 'a-good-lesson';
    $lessonSlugUsed = 'a-good-lesson-1';
    $tutorialTitle = 'Tutorial';
    $tutorialSlug = 'tutorial-slug';
    $tutorialId = 1;
    $tutorial = [
      'tutorial_id' => $tutorialId,
      'tutorial_title' => $tutorialTitle
    ];
    $formData = [
      'lesson_title' => $lessonTitle,
      'slug' => $tutorialSlug,
      'video_name' => $videoName,
      'seconds' => $seconds,
      'lesson_security_level_id' => $securityLevelId,
      'lesson_description' => 'So short.',
      'lesson_security_level_id' => $securityLevelId,
      'display_order' => $displayOrder,
      'lesson_slug' => $lessonSlugUsed,
      'tutorial_id' => $tutorialId
    ];
    $this->tutorialRepository->shouldReceive('getLessonByTitleAndTutorialId')
      ->once()
      ->with($lessonTitle, $tutorial['tutorial_id'])
      ->andReturn(NULL);
    $this->tutorialRepository->shouldReceive('getLessonSecurityLevelById')
      ->once()
      ->with($securityLevelId)
      ->andReturn($securityLevel);
    $this->tutorialRepository->shouldReceive('getTutorialBySlug')
      ->once()
      ->with($tutorialSlug)
      ->andReturn($tutorial);
    $this->tutorialRepository->shouldReceive('getLessonBySlugAndTutorialId')
      ->once()
      ->with($lessonSlug, $tutorialId)
      ->andReturn(true);
    $this->tutorialRepository->shouldReceive('getLessonBySlugAndTutorialId')
      ->once()
      ->with($lessonSlugUsed, $tutorialId)
      ->andReturn(NULL);
    $this->tutorialRepository->shouldReceive('insertLesson')
      ->once()
      ->with($formData)
      ->andReturn(4);

    $success = $this->lessonFormService->createLesson($formData);
    $flashMessage = $this->lessonFormService->getFlashMessage();
    $errors = $this->lessonFormService->getErrors();
    $expectedErrors = [];
    $this->assertTrue($success);
    $this->assertNull($flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testUpdateLessonWithNoFormData() {
    $formData = [];

    $success = $this->lessonFormService->updateLesson($formData);
    $flashMessage = $this->lessonFormService->getFlashMessage();
    $errors = $this->lessonFormService->getErrors();
    $expectedFlashMessage = "Sorry, something has gone wrong. Let's start again.";
    $expectedErrors = [];
    $this->assertFalse($success);
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testUpdateLessonWithDuplicateTitle() {
    $lessonId = 99;
    $lessonTitle = 'A Good Lesson';
    $securityLevelId = 1;
    $securityLevel = ['lesson_security_level_id' => 1, 'description' => 'Free'];
    $displayOrder = 1;
    $videoName = 'long-lesson.mp4';
    $seconds = 120;
    $lessonSlug = 'a-good-lesson';
    $tutorialTitle = 'Tutorial';
    $tutorialSlug = 'tutorial-slug';
    $tutorialId = 1;
    $tutorial = [
      'tutorial_id' => $tutorialId,
      'tutorial_title' => $tutorialTitle
    ];
    $updatedDuplicateTitle = 'Updated Lesson';
    $lesson = [
      'lesson_id' => $lessonId,
      'lesson_title' => $updatedDuplicateTitle,
      'lesson_slug' => 'a-different-lesson'
    ];
    $formData = [
      'lesson_title' => $updatedDuplicateTitle,
      'slug' => $tutorialSlug,
      'video_name' => $videoName,
      'seconds' => $seconds,
      'lesson_security_level_id' => $securityLevelId,
      'lesson_description' => 'So short.',
      'lesson_security_level_id' => $securityLevelId,
      'display_order' => $displayOrder,
      'lesson_slug' => $lessonSlug,
      'tutorial_id' => $tutorialId
    ];
    $this->tutorialRepository->shouldReceive('getTutorialBySlug')
      ->once()
      ->with($tutorialSlug)
      ->andReturn($tutorial);
    $this->tutorialRepository->shouldReceive('getLessonBySlugs')
      ->once()
      ->with($tutorialSlug, $lessonSlug)
      ->andReturn($lesson);
    $this->tutorialRepository->shouldReceive('getLessonByTitleAndTutorialId')
      ->once()
      ->with($updatedDuplicateTitle, $tutorial['tutorial_id'])
      ->andReturn($lesson);
    $this->tutorialRepository->shouldReceive('getLessonSecurityLevelById')
      ->once()
      ->with($securityLevelId)
      ->andReturn($securityLevel);

    $success = $this->lessonFormService->updateLesson($formData);
    $flashMessage = $this->lessonFormService->getFlashMessage();
    $errors = $this->lessonFormService->getErrors();
    $expectedFlashMessage = "There were some errors. Please fix these and then we will update the lesson.";
    $expectedErrors = [
      'lesson_title' => 'The title is already used by another lesson within this tutorial.'
    ];
    $this->assertFalse($success);
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testUpdateLessonWithValidData() {
    $lessonId = 99;
    $lessonTitle = 'A Good Lesson';
    $securityLevelId = 1;
    $securityLevel = ['lesson_security_level_id' => 1, 'description' => 'Free'];
    $displayOrder = 1;
    $videoName = 'long-lesson.mp4';
    $seconds = 120;
    $lessonSlug = 'a-good-lesson';
    $tutorialTitle = 'Tutorial';
    $tutorialSlug = 'tutorial-slug';
    $tutorialId = 1;
    $tutorial = [
      'tutorial_id' => $tutorialId,
      'tutorial_title' => $tutorialTitle
    ];
    $lesson = [
      'lesson_id' => $lessonId,
      'lesson_title' => $lessonTitle,
      'lesson_slug' => $lessonSlug
    ];
    $formData = [
      'lesson_title' => $lessonTitle,
      'slug' => $tutorialSlug,
      'video_name' => $videoName,
      'seconds' => $seconds,
      'lesson_security_level_id' => $securityLevelId,
      'lesson_description' => 'So short.',
      'lesson_security_level_id' => $securityLevelId,
      'display_order' => $displayOrder,
      'lesson_slug' => $lessonSlug,
      'tutorial_id' => $tutorialId
    ];
    $this->tutorialRepository->shouldReceive('getTutorialBySlug')
      ->once()
      ->with($tutorialSlug)
      ->andReturn($tutorial);
    $this->tutorialRepository->shouldReceive('getLessonBySlugs')
      ->once()
      ->with($tutorialSlug, $lessonSlug)
      ->andReturn($lesson);
    $this->tutorialRepository->shouldReceive('getLessonByTitleAndTutorialId')
      ->once()
      ->with($lessonTitle, $tutorial['tutorial_id'])
      ->andReturn(NULL);
    $this->tutorialRepository->shouldReceive('getLessonSecurityLevelById')
      ->once()
      ->with($securityLevelId)
      ->andReturn($securityLevel);
    $this->tutorialRepository->shouldReceive('updateLessonByTutorialIdAndSlug')
      ->once()
      ->with($formData)
      ->andReturn(true);

    $success = $this->lessonFormService->updateLesson($formData);
    $flashMessage = $this->lessonFormService->getFlashMessage();
    $errors = $this->lessonFormService->getErrors();
    $expectedErrors = [];
    $this->assertTrue($success);
    $this->assertNull($flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }
}
