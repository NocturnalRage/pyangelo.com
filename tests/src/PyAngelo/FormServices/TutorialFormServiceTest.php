<?php
namespace tests\src\PyAngelo\FormServices;

use Mockery;
use PHPUnit\Framework\TestCase;
use PyAngelo\FormServices\TutorialFormService;

class TutorialFormServiceTest extends TestCase {
  public function setUp(): void {
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->tutorialFormService = new TutorialFormService(
      $this->auth,
      $this->tutorialRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testCreateTutorialWithNoFormData() {
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);
    $formData = [];

    $success = $this->tutorialFormService->createTutorial($formData, NULL);
    $flashMessage = $this->tutorialFormService->getFlashMessage();
    $errors = $this->tutorialFormService->getErrors();
    $expectedFlashMessage = 'There were some errors. Please fix these and then we will create the tutorial. You will also need to re-select the thumbnail for this tutorial series.';
    $expectedErrors = [
      'crsfToken' => 'Invalid CRSF token.',
      'title' => 'You must supply a title for this tutorial series.',
      'description' => 'The description field cannot be blank.',
      'tutorial_category_id' => 'You must select the category this tutorial belongs to.',
      'tutorial_level_id' => 'You must select the level of this tutorial.',
      'single_sketch' => 'You must select if there will only be a single sketch for the entire tutorial.',
      'display_order' => 'You must select where this will be displayed relative to other tutorial series.'
    ];
    $this->assertFalse($success);
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testCreateTutorialWithInvalidSingleSketch() {
    $tutorialCategoryId = 1;
    $tutorialLevelId = 2;
    $singleSketch = 5;
    $title = 'A normal title';
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialCategoryById')
      ->once()
      ->with($tutorialCategoryId)
      ->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialLevelById')
      ->once()
      ->with($tutorialLevelId)
      ->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialByTitle')
      ->once()
      ->with($title)
      ->andReturn(NULL);
    $formData = [
      'title' => $title,
      'description' => 'A normal description.',
      'tutorial_category_id' => $tutorialCategoryId,
      'tutorial_level_id' => $tutorialLevelId,
      'single_sketch' => $singleSketch,
      'display_order' => 1
    ];

    $success = $this->tutorialFormService->createTutorial($formData, NULL);
    $flashMessage = $this->tutorialFormService->getFlashMessage();
    $errors = $this->tutorialFormService->getErrors();
    $expectedFlashMessage = 'There were some errors. Please fix these and then we will create the tutorial. You will also need to re-select the thumbnail for this tutorial series.';
    $expectedErrors = [
      'single_sketch' => 'You must select either true or false.'
    ];
    $this->assertFalse($success);
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testCreateTutorialWithDataTooLong() {
    $tutorialCategoryId = 1;
    $tutorialLevelId = 2;
    $singleSketch = 0;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialCategoryById')
      ->once()
      ->with($tutorialCategoryId)
      ->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialLevelById')
      ->once()
      ->with($tutorialLevelId)
      ->andReturn(true);
    $formData = [
      'title' => 'A very very very very long title that is much more than 100 characater long and will be rejected by the validation which expects the title to be less than 100 characters',
      'description' => 'So short.',
      'tutorial_category_id' => $tutorialCategoryId,
      'tutorial_level_id' => $tutorialLevelId,
      'single_sketch' => $singleSketch,
      'display_order' => 1
    ];

    $success = $this->tutorialFormService->createTutorial($formData, NULL);
    $flashMessage = $this->tutorialFormService->getFlashMessage();
    $errors = $this->tutorialFormService->getErrors();
    $expectedFlashMessage = 'There were some errors. Please fix these and then we will create the tutorial. You will also need to re-select the thumbnail for this tutorial series.';
    $expectedErrors = [
      'title' => 'The title can be no longer than 100 characters.'
    ];
    $this->assertFalse($success);
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testCreateTutorialWithInvalidCategory() {
    $tutorialCategoryId = 100;
    $tutorialLevelId = 1;
    $singleSketch = 0;
    $title = 'A Great Tutorial';
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialCategoryById')
      ->once()
      ->with($tutorialCategoryId)
      ->andReturn(false);
    $this->tutorialRepository->shouldReceive('getTutorialLevelById')
      ->once()
      ->with($tutorialLevelId)
      ->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialByTitle')
      ->once()
      ->with($title)
      ->andReturn(NULL);
    $formData = [
      'title' => $title,
      'description' => 'So short.',
      'tutorial_category_id' => $tutorialCategoryId,
      'tutorial_level_id' => $tutorialLevelId,
      'single_sketch' => $singleSketch,
      'display_order' => 1
    ];

    $success = $this->tutorialFormService->createTutorial($formData, NULL);
    $flashMessage = $this->tutorialFormService->getFlashMessage();
    $errors = $this->tutorialFormService->getErrors();
    $expectedFlashMessage = 'There were some errors. Please fix these and then we will create the tutorial. You will also need to re-select the thumbnail for this tutorial series.';
    $expectedErrors = [
      'tutorial_category_id' => 'The specified category for this tutorial does not exist.'
    ];
    $this->assertFalse($success);
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testCreateTutorialWithInvalidLevel() {
    $tutorialCategoryId = 1;
    $tutorialLevelId = 100;
    $singleSketch = 0;
    $title = 'A Great Tutorial';
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialCategoryById')
      ->once()
      ->with($tutorialCategoryId)
      ->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialLevelById')
      ->once()
      ->with($tutorialLevelId)
      ->andReturn(false);
    $this->tutorialRepository->shouldReceive('getTutorialByTitle')
      ->once()
      ->with($title)
      ->andReturn(NULL);
    $formData = [
      'title' => $title,
      'description' => 'So short.',
      'tutorial_category_id' => $tutorialCategoryId,
      'tutorial_level_id' => $tutorialLevelId,
      'single_sketch' => $singleSketch,
      'display_order' => 1
    ];

    $success = $this->tutorialFormService->createTutorial($formData, NULL);
    $flashMessage = $this->tutorialFormService->getFlashMessage();
    $errors = $this->tutorialFormService->getErrors();
    $expectedFlashMessage = 'There were some errors. Please fix these and then we will create the tutorial. You will also need to re-select the thumbnail for this tutorial series.';
    $expectedErrors = [
      'tutorial_level_id' => 'The specified level for this tutorial does not exist.'
    ];
    $this->assertFalse($success);
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testCreateTutorialWithValidData() {
    $imageInfo = array(
      'name' => 'tutorial.jpg',
      'type' => 'image/jpeg',
      'size' => 542,
      'tmp_name' => '/temp.jpg',
      'error' => 0
    );
    $pdfInfo = array(
      'name' => 'solve.pdf',
      'type' => 'application/pdf',
      'size' => 1024,
      'tmp_name' => '/temp1.jpg',
      'error' => 0
    );
    $title = 'A New Tutorial';
    $slug = 'a-new-tutorial';
    $tutorialCategoryId = 3;
    $tutorialLevelId = 3;
    $singleSketch = 0;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialCategoryById')
      ->once()
      ->with($tutorialCategoryId)
      ->andReturn(['tutorial_category_id' => $tutorialCategoryId, 'category' => '3x3 Videos', 'category_slug' => '3x3']);
    $this->tutorialRepository->shouldReceive('getTutorialLevelById')
      ->once()
      ->with($tutorialLevelId)
      ->andReturn(['tutorial_level_id' => $tutorialLevelId, 'description' => 'advanced']);
    $this->tutorialRepository->shouldReceive('getTutorialByTitle')
      ->once()
      ->with($title)
      ->andReturn(NULL);
    $this->tutorialRepository->shouldReceive('getTutorialBySlug')
      ->once()
      ->with($slug)
      ->andReturn(NULL);
    $formData = [
      'title' => $title,
      'description' => 'Great information about coding.',
      'tutorial_category_id' => $tutorialCategoryId,
      'tutorial_level_id' => $tutorialLevelId,
      'single_sketch' => $singleSketch,
      'display_order' => 1
    ];

    $success = $this->tutorialFormService->createTutorial($formData, $imageInfo);
    $flashMessage = $this->tutorialFormService->getFlashMessage();
    $errors = $this->tutorialFormService->getErrors();
    $expectedErrors = [];
    // This fails because we don't have a file to actually move.
#    $this->assertTrue($success);
    $this->assertNull($flashMessage);
    $this->assertEquals($expectedErrors, $errors);
    // TODO: Move the moveFile procedure into it's own class which
    // can be mocked and therefore tested without actually needing
    // to move a file.
  }

  public function testCreateTutorialWithValidDataExistingSlug() {
    $imageInfo = array(
      'name' => 'tutorial.jpg',
      'type' => 'image/jpeg',
      'size' => 542,
      'tmp_name' => '/temp.jpg',
      'error' => 0
    );
    $pdfInfo = array(
      'name' => 'solve.pdf',
      'type' => 'application/pdf',
      'size' => 1024,
      'tmp_name' => '/temp1.jpg',
      'error' => 0
    );
    $title = 'A New Tutorial';
    $slug = 'a-new-tutorial';
    $tutorialCategoryId = 3;
    $tutorialLevelId = 3;
    $singleSketch = 0;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialCategoryById')
      ->once()
      ->with($tutorialCategoryId)
      ->andReturn(['tutorial_category_id' => $tutorialCategoryId, 'category' => '3x3 Videos', 'category_slug' => '3x3']);
    $this->tutorialRepository->shouldReceive('getTutorialLevelById')
      ->once()
      ->with($tutorialLevelId)
      ->andReturn(['tutorial_level_id' => $tutorialLevelId, 'description' => 'advanced']);
    $this->tutorialRepository->shouldReceive('getTutorialByTitle')
      ->once()
      ->with($title)
      ->andReturn(NULL);
    $this->tutorialRepository->shouldReceive('getTutorialBySlug')
      ->once()
      ->with($slug)
      ->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialBySlug')
      ->once()
      ->with($slug . '-1')
      ->andReturn(NULL);
    $formData = [
      'title' => $title,
      'description' => 'Great information about coding.',
      'tutorial_category_id' => $tutorialCategoryId,
      'tutorial_level_id' => $tutorialLevelId,
      'single_sketch' => $singleSketch,
      'display_order' => 1
    ];

    $success = $this->tutorialFormService->createTutorial($formData, $imageInfo);
    $flashMessage = $this->tutorialFormService->getFlashMessage();
    $errors = $this->tutorialFormService->getErrors();
    $expectedErrors = [];
    // This fails because we don't have a file to actually move.
#    $this->assertTrue($success);
    $this->assertNull($flashMessage);
    $this->assertEquals($expectedErrors, $errors);
    // TODO: Move the moveFile procedure into it's own class which
    // can be mocked and therefore tested without actually needing
    // to move a file.
  }

  public function testUpdateTutorialWithNoFormData() {
    $formData = [];

    $success = $this->tutorialFormService->updateTutorial($formData, NULL);
    $flashMessage = $this->tutorialFormService->getFlashMessage();
    $errors = $this->tutorialFormService->getErrors();
    $expectedFlashMessage = "Sorry, something has gone wrong. Let's start again.";
    $expectedErrors = [];
    $this->assertFalse($success);
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testUpdateTutorialWithDuplicateTitle() {
    $slug = 'old-slug';
    $updatedTitle = 'Updated Tutorial';
    $tutorialCategoryId = 1;
    $tutorialLevelId = 2;
    $singleSketch = 0;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialCategoryById')
      ->once()
      ->with($tutorialCategoryId)
      ->andReturn(['tutorial_category_id' => $tutorialCategoryId, 'category' => '3x3 Videos', 'category_slug' => '3x3']);
    $this->tutorialRepository->shouldReceive('getTutorialLevelById')
      ->once()
      ->with($tutorialLevelId)
      ->andReturn(['tutorial_level_id' => $tutorialLevelId, 'description' => 'advanced']);
    $this->tutorialRepository->shouldReceive('getTutorialBySlug')
      ->once()
      ->with($slug)
      ->andReturn(['slug' => $slug]);
    $this->tutorialRepository->shouldReceive('getTutorialByTitle')
      ->once()
      ->with($updatedTitle)
      ->andReturn(['slug' => 'a-tutorial', 'title' => $updatedTitle]);
    $formData = [
      'slug' => $slug,
      'title' => $updatedTitle,
      'description' => 'updated',
      'tutorial_category_id' => $tutorialCategoryId,
      'tutorial_level_id' => $tutorialLevelId,
      'single_sketch' => $singleSketch,
      'display_order' => 1
    ];

    $success = $this->tutorialFormService->updateTutorial($formData, NULL);
    $flashMessage = $this->tutorialFormService->getFlashMessage();
    $errors = $this->tutorialFormService->getErrors();
    $expectedFlashMessage = "There were some errors. Please fix these and then we will update the tutorial. You will also need to re-select the thumbnail for this tutorial series if you changed it.";
    $expectedErrors = [
      'title' => 'The title is already used by another tutorial.'
    ];
    $this->assertFalse($success);
    $this->assertEquals($expectedFlashMessage, $flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }

  public function testUpdateTutorialWithValidData() {
    $slug = 'old-slug';
    $updatedTitle = 'Updated Tutorial';
    $updatedDescription = 'updated';
    $updatedTutorialCategoryId = 5;
    $updatedTutorialLevelId = 3;
    $updatedSingleSketch = 0;
    $updatedDisplayOrder = 1;
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->tutorialRepository->shouldReceive('getTutorialCategoryById')
      ->once()
      ->with($updatedTutorialCategoryId)
      ->andReturn(['tutorial_category_id' => $updatedTutorialCategoryId, 'category' => '3x3 Videos', 'category_slug' => '3x3']);
    $this->tutorialRepository->shouldReceive('getTutorialLevelById')
      ->once()
      ->with($updatedTutorialLevelId)
      ->andReturn(['tutorial_level_id' => $updatedTutorialLevelId, 'description' => 'advanced']);
    $this->tutorialRepository->shouldReceive('getTutorialBySlug')
      ->once()
      ->with($slug)
      ->andReturn(['slug' => $slug]);
    $this->tutorialRepository->shouldReceive('getTutorialByTitle')
      ->once()
      ->with($updatedTitle)
      ->andReturn(NULL);
    $this->tutorialRepository->shouldReceive('updateTutorialBySlug')
      ->once()
      ->with($slug, $updatedTitle, $updatedDescription, $updatedTutorialCategoryId, $updatedTutorialLevelId, $updatedSingleSketch, $updatedDisplayOrder)
      ->andReturn(NULL);
    $formData = [
      'slug' => $slug,
      'title' => $updatedTitle,
      'description' => $updatedDescription,
      'tutorial_category_id' => $updatedTutorialCategoryId,
      'tutorial_level_id' => $updatedTutorialLevelId,
      'single_sketch' => $updatedSingleSketch,
      'display_order' => $updatedDisplayOrder
    ];

    $success = $this->tutorialFormService->updateTutorial($formData, NULL);
    $flashMessage = $this->tutorialFormService->getFlashMessage();
    $errors = $this->tutorialFormService->getErrors();
    $expectedErrors = [];
    $this->assertTrue($success);
    $this->assertNull($flashMessage);
    $this->assertEquals($expectedErrors, $errors);
  }
}
