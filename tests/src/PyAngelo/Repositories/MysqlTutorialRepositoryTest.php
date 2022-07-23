<?php
namespace Tests\PyAngelo\Repositories;

use PHPUnit\Framework\TestCase;
use PyAngelo\Repositories\MysqlTutorialRepository;

class MysqlTutorialRepositoryTest extends TestCase {
  protected $dbh;
  protected $tutorialRepository;

  public function setUp(): void {
    $dotenv  = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../../../', '.env.test');
    $dotenv->load();
    $this->dbh = new \Mysqli(
      $_ENV['DB_HOST'],
      $_ENV['DB_USERNAME'],
      $_ENV['DB_PASSWORD'],
      $_ENV['DB_DATABASE']
    );
    $this->dbh->begin_transaction();
    $this->tutorialRepository = new MysqlTutorialRepository($this->dbh);
  }

  public function tearDown(): void {
    $this->dbh->rollback();
    $this->dbh->close();
  }

  public function testInsertTutorialAndGetTutorials() {
    $rowsInserted = $this->tutorialRepository->insertTutorialLevel(1, 'Beginner');
    $this->assertSame(1, $rowsInserted);
    $rowsInserted = $this->tutorialRepository->insertTutorialLevel(2, 'Advanced');
    $this->assertSame(1, $rowsInserted);

    $tutorialLevel = $this->tutorialRepository->getTutorialLevelById(2);
    $expectedTutorialLevel = [
      'tutorial_level_id' => 2,
      'description' => 'Advanced'
    ];
    $this->assertSame($expectedTutorialLevel, $tutorialLevel);

    $levels = $this->tutorialRepository->getAllTutorialLevels();
    $this->assertEquals($expectedTutorialLevel, $levels[1]);

    $rowsInserted = $this->tutorialRepository->insertTutorialCategory(1, '3x3 Videos', '3x3', 1);
    $this->assertSame(1, $rowsInserted);
    $rowsInserted = $this->tutorialRepository->insertTutorialCategory(2, '3x3 Algorithms', '3x3-algs', 2);
    $this->assertSame(1, $rowsInserted);

    $tutorialCategory = $this->tutorialRepository->getTutorialCategoryById(2);
    $expectedTutorialCategory = [
      'tutorial_category_id' => 2,
      'category' => '3x3 Algorithms',
      'category_slug' => '3x3-algs',
      'display_order' => 2
    ];
    $this->assertSame($expectedTutorialCategory, $tutorialCategory);

    $categories = $this->tutorialRepository->getAllTutorialCategories();
    $this->assertEquals($expectedTutorialCategory, $categories[1]);

    $title = 'Test Tutorial';
    $description = 'A test tutorial.';
    $slug = 'test-tutorial';
    $tutorialCategoryId = 1;
    $tutorialLevelId = 1;
    $singleSketch = 0;
    $tutorialSketchId = NULL;
    $displayOrder = 1;
    $thumbnail = 'test-tutorial.jpg';
    $expectedDeletedCount = 2;

    // Insert and retrieve data from the table
    $tutorialId1 = $this->tutorialRepository->insertTutorial(
      $title, $description, $slug, $tutorialCategoryId, $tutorialLevelId, $singleSketch, $tutorialSketchId, $displayOrder, $thumbnail
    );
    $tutorial = $this->tutorialRepository->getTutorialBySlug($slug);
    $this->assertSame($tutorial['title'], $title);
    $this->assertSame($tutorial['slug'], $slug);
    $this->assertSame($tutorial['tutorial_level_id'], $tutorialLevelId);
    $this->assertSame($tutorial['tutorial_category_id'], $tutorialCategoryId);
    $this->assertSame($tutorial['display_order'], $displayOrder);
    $this->assertSame($tutorial['thumbnail'], $thumbnail);
    $tutorial = $this->tutorialRepository->getTutorialByTitle($title);
    $this->assertSame($tutorial['title'], $title);
    $this->assertSame($tutorial['slug'], $slug);
    $this->assertSame($tutorial['tutorial_level_id'], $tutorialLevelId);
    $this->assertSame($tutorial['tutorial_category_id'], $tutorialCategoryId);
    $this->assertSame($tutorial['display_order'], $displayOrder);
    $this->assertSame($tutorial['thumbnail'], $thumbnail);

    $title = 'Test Tutorial 2';
    $description = 'A second tutorial.';
    $slug = 'test-tutorial-2';
    $tutorialCategoryId = 1;
    $tutorialLevelId = 1;
    $singleSketch = 1;
    $tutorialSketchId = NULL;
    $displayOrder = 2;
    $thumbnail = 'test-tutorial-2.jpg';
    $tutorialId2 = $this->tutorialRepository->insertTutorial(
      $title, $description, $slug, $tutorialCategoryId, $tutorialLevelId, $singleSketch, $tutorialSketchId, $displayOrder, $thumbnail
    );

    $description = 'A second tutorial twice removed.';
    $tutorialLevelId = 2;
    $tutorialCategoryId = 2;
    $singleSketch = 0;
    $rowsUpdated = $this->tutorialRepository->updateTutorialBySlug(
      $slug, $title, $description, $tutorialLevelId, $tutorialCategoryId, $singleSketch, $tutorialSketchId, $displayOrder
    );
    $this->assertSame(1, $rowsUpdated);
    $tutorial = $this->tutorialRepository->getTutorialBySlug($slug);
    $this->assertSame($tutorial['title'], $title);
    $this->assertSame($tutorial['tutorial_level_id'], $tutorialLevelId);
    $this->assertSame($tutorial['tutorial_category_id'], $tutorialCategoryId);
    $this->assertSame($tutorial['single_sketch'], $singleSketch);
    $this->assertSame($tutorial['display_order'], $displayOrder);
    $this->assertSame($tutorial['description'], $description);

    $updatedDisplayOrder = 3;
    $rowsUpdated = $this->tutorialRepository->updateTutorialOrder($slug, $updatedDisplayOrder);
    $this->assertSame(1, $rowsUpdated);
    $tutorial = $this->tutorialRepository->getTutorialBySlug($slug);
    $this->assertSame($updatedDisplayOrder, $tutorial['display_order']);

    $updatedThumbnail = 'great-test-tutorial-2.jpg';
    $rowsUpdated = $this->tutorialRepository->updateTutorialThumbnailBySlug(
      $slug, $updatedThumbnail
    );
    $this->assertSame(1, $rowsUpdated);
    $tutorial = $this->tutorialRepository->getTutorialBySlug($slug);
    $this->assertSame($updatedThumbnail, $tutorial['thumbnail']);

    $updatedPdf = 'my-great-pdf.pdf';
    $rowsUpdated = $this->tutorialRepository->updateTutorialPdfBySlug(
      $slug, $updatedPdf
    );
    $this->assertSame(1, $rowsUpdated);
    $tutorial = $this->tutorialRepository->getTutorialBySlug($slug);
    $this->assertSame($updatedPdf, $tutorial['pdf']);

    $tutorials = $this->tutorialRepository->getTutorialsByCategory('3x3');
    $this->assertCount(1, $tutorials);
    $tutorials = $this->tutorialRepository->getTutorialsByCategory('3x3-algs');
    $this->assertCount(1, $tutorials);

    $tutorials = $this->tutorialRepository->getAllTutorials();
    $this->assertCount(2, $tutorials);

    $rowsInserted = $this->tutorialRepository->insertLessonSecurityLevel(1, 'Free members');
    $this->assertSame(1, $rowsInserted);
    $rowsInserted = $this->tutorialRepository->insertLessonSecurityLevel(2, 'Premium members');
    $this->assertSame(1, $rowsInserted);

    $lessonSecurityLevels = $this->tutorialRepository->getAllLessonSecurityLevels();
    $expectedLessonSecurityLevels = [
      [
        'lesson_security_level_id' => 1,
        'description' => 'Free members'
      ],
      [
        'lesson_security_level_id' => 2,
        'description' => 'Premium members'
      ]
    ];
    $this->assertEquals($expectedLessonSecurityLevels, $lessonSecurityLevels);

    $lessonSecurityLevel = $this->tutorialRepository->getLessonSecurityLevelById(1);
    $this->assertEquals($expectedLessonSecurityLevels[0], $lessonSecurityLevel);

    $lessonInfo1 = [
      'tutorial_id' => $tutorialId1,
      'lesson_title' => 'A New Lesson',
      'lesson_description' => 'An important description.',
      'video_name' => 'lesson.mp4',
      'youtube_url' => '',
      'seconds' => '120',
      'lesson_slug' => 'a-new-lesson',
      'lesson_security_level_id' => 1,
      'display_order' => 1
    ];
    $expectedSlugLesson1 = 'a-new-lesson';
    $lesson1 = $this->tutorialRepository->insertLesson($lessonInfo1);
    $lessonInfo2 = [
      'tutorial_id' => $tutorialId1,
      'lesson_title' => 'New Lesson 2',
      'lesson_description' => 'An important description.',
      'video_name' => 'lesson-2.mp4',
      'youtube_url' => '',
      'seconds' => '45',
      'lesson_slug' => 'new-lesson-2',
      'lesson_security_level_id' => 2,
      'display_order' => 2
    ];
    $lesson2 = $this->tutorialRepository->insertLesson($lessonInfo2);
    $lessonInfo3 = [
      'tutorial_id' => $tutorialId2,
      'lesson_title' => 'Part of Tutorial 2',
      'lesson_description' => 'Tutorial 2 lesson.',
      'video_name' => 'lesson-3.mp4',
      'youtube_url' => '',
      'seconds' => '29',
      'lesson_slug' => 'part-of-tutorial-2',
      'lesson_security_level_id' => 1,
      'display_order' => 1
    ];
    $expectedSlugLesson3 = 'part-of-tutorial-2';
    $lesson3 = $this->tutorialRepository->insertLesson($lessonInfo3);

    $lessons = $this->tutorialRepository->getTutorialLessons($tutorialId1, 0);
    $this->assertEquals($expectedSlugLesson1, $lessons[0]['lesson_slug']);
    $this->assertEquals($lessons[0]['lesson_id'], $lesson1);
    $this->assertEquals($lessons[0]['tutorial_id'], $lessonInfo1['tutorial_id']);
    $this->assertEquals($lessons[0]['tutorial_slug'], 'test-tutorial');
    $this->assertEquals($lessons[0]['lesson_title'], $lessonInfo1['lesson_title']);
    $this->assertEquals($lessons[0]['lesson_description'], $lessonInfo1['lesson_description']);
    $this->assertEquals($lessons[0]['video_name'], $lessonInfo1['video_name']);
    $this->assertEquals($lessons[0]['seconds'], $lessonInfo1['seconds']);
    $this->assertEquals($lessons[0]['display_duration'], '2:00');
    $this->assertEquals($lessons[0]['lesson_slug'], $lessonInfo1['lesson_slug']);
    $this->assertEquals($lessons[0]['lesson_security_level_id'], $lessonInfo1['lesson_security_level_id']);
    $this->assertEquals($lessons[0]['display_order'], $lessonInfo1['display_order']);
    $this->assertNotNull($lessons[0]['created_at']);
    $this->assertNotNull($lessons[0]['updated_at']);
    $this->assertEquals($lessons[1]['lesson_id'], $lesson2);
    $this->assertEquals($lessons[1]['tutorial_id'], $lessonInfo2['tutorial_id']);
    $this->assertEquals($lessons[1]['lesson_title'], $lessonInfo2['lesson_title']);
    $this->assertEquals($lessons[1]['lesson_description'], $lessonInfo2['lesson_description']);
    $this->assertEquals($lessons[1]['video_name'], $lessonInfo2['video_name']);
    $this->assertEquals($lessons[1]['seconds'], $lessonInfo2['seconds']);
    $this->assertEquals($lessons[1]['display_duration'], '0:45');
    $this->assertEquals($lessons[1]['lesson_slug'], $lessonInfo2['lesson_slug']);
    $this->assertEquals($lessons[1]['lesson_security_level_id'], $lessonInfo2['lesson_security_level_id']);
    $this->assertEquals($lessons[1]['display_order'], $lessonInfo2['display_order']);
    $this->assertNotNull($lessons[1]['created_at']);
    $this->assertNotNull($lessons[1]['updated_at']);
    $this->assertCount(2, $lessons);

    $lesson = $this->tutorialRepository->getLessonBySlugs($slug, $expectedSlugLesson3);
    $this->assertEquals($lesson['tutorial_id'], $tutorialId2);
    $this->assertEquals($lesson['tutorial_title'], 'Test Tutorial 2');
    $this->assertEquals($lesson['tutorial_slug'], 'test-tutorial-2');
    $this->assertEquals($lesson['tutorial_thumbnail'], $updatedThumbnail);
    $this->assertEquals($lesson['lesson_title'], $lessonInfo3['lesson_title']);
    $this->assertEquals($lesson['lesson_description'], $lessonInfo3['lesson_description']);
    $this->assertEquals($lesson['lesson_slug'], $lessonInfo3['lesson_slug']);
    $this->assertEquals($lesson['seconds'], $lessonInfo3['seconds']);
    $this->assertEquals($lesson['video_name'], $lessonInfo3['video_name']);
    $this->assertEquals($lesson['lesson_security_level_id'], $lessonInfo3['lesson_security_level_id']);
    $this->assertEquals($lesson['display_order'], $lessonInfo3['display_order']);

    $lesson = $this->tutorialRepository->getLessonBySlugAndTutorialId($expectedSlugLesson3, $tutorialId2);
    $this->assertEquals($lesson['lesson_title'], $lessonInfo3['lesson_title']);
    $this->assertEquals($lesson['lesson_description'], $lessonInfo3['lesson_description']);

    $lesson = $this->tutorialRepository->getLessonByTitleAndTutorialId($lessonInfo3['lesson_title'], $lessonInfo3['tutorial_id']);
    $this->assertEquals($lesson['tutorial_id'], $tutorialId2);
    $this->assertEquals($lesson['lesson_title'], $lessonInfo3['lesson_title']);
    $this->assertEquals($lesson['lesson_description'], $lessonInfo3['lesson_description']);
    $this->assertEquals($lesson['lesson_slug'], $lessonInfo3['lesson_slug']);
    $this->assertEquals($lesson['seconds'], $lessonInfo3['seconds']);
    $this->assertEquals($lesson['video_name'], $lessonInfo3['video_name']);
    $this->assertEquals($lesson['lesson_security_level_id'], $lessonInfo3['lesson_security_level_id']);
    $this->assertEquals($lesson['display_order'], $lessonInfo3['display_order']);
    $lesson = $this->tutorialRepository->getLessonById($lesson3);
    $this->assertEquals($lesson['lesson_title'], $lessonInfo3['lesson_title']);
    $this->assertEquals($lesson['lesson_description'], $lessonInfo3['lesson_description']);
    $this->assertEquals($lesson['lesson_slug'], $lessonInfo3['lesson_slug']);
    $this->assertEquals($lesson['seconds'], $lessonInfo3['seconds']);
    $this->assertEquals($lesson['video_name'], $lessonInfo3['video_name']);
    $this->assertEquals($lesson['lesson_security_level_id'], $lessonInfo3['lesson_security_level_id']);
    $this->assertEquals($lesson['display_order'], $lessonInfo3['display_order']);
    $lessonWithStatus = $this->tutorialRepository->getLessonBySlugsWithStatus($slug, $expectedSlugLesson3, 0);
    $this->assertEquals($lessonWithStatus['completed'], 0);
    $this->assertEquals($lessonWithStatus['favourited'], 0);

    $nextLesson = $this->tutorialRepository->getNextLessonInTutorial($tutorialId1, 1);
    $this->assertEquals($nextLesson['lesson_id'], $lesson2);

    $formData = [
      'lesson_title' => 'New Title',
      'lesson_description' => 'New Description',
      'video_name' => 'new-video.mp4',
      'youtube_url' => 'https://youtube.url',
      'seconds' => 200,
      'lesson_security_level_id' => 1,
      'display_order' => 10,
      'lesson_slug' => $expectedSlugLesson3,
      'tutorial_id' => $tutorialId2
    ];
    $rowsUpdated = $this->tutorialRepository->updateLessonByTutorialIdAndSlug($formData);
    $this->assertEquals(1, $rowsUpdated);
    $lesson = $this->tutorialRepository->getLessonBySlugs($slug, $expectedSlugLesson3);
    $this->assertEquals($lesson['tutorial_id'], $tutorialId2);
    $this->assertEquals($lesson['lesson_title'], 'New Title');
    $this->assertEquals($lesson['lesson_description'], 'New Description');
    $this->assertEquals($lesson['lesson_slug'], $expectedSlugLesson3);
    $this->assertEquals($lesson['seconds'], 200);
    $this->assertEquals($lesson['video_name'], 'new-video.mp4');
    $this->assertEquals($lesson['youtube_url'], 'https://youtube.url');
    $this->assertEquals($lesson['lesson_security_level_id'], 1);
    $this->assertEquals($lesson['display_order'], 10);

    $updatedPoster = 'my-great-lesson.png';
    $rowsUpdated = $this->tutorialRepository->updateLessonPosterByTutorialIdAndSlug(
      $tutorialId2, $expectedSlugLesson3, $updatedPoster
    );
    $this->assertSame(1, $rowsUpdated);
    $lesson = $this->tutorialRepository->getLessonBySlugAndTutorialId($expectedSlugLesson3, $tutorialId2);
    $this->assertSame($updatedPoster, $lesson['poster']);

    $rowsUpdated = $this->tutorialRepository->updateLessonOrder(
      $tutorialId2,
      $expectedSlugLesson3,
      1
    );
    $this->assertSame(1, $rowsUpdated);
    $lesson = $this->tutorialRepository->getLessonBySlugs($slug, $expectedSlugLesson3);
    $this->assertSame(1, $lesson['display_order']);

    $personId = 0;
    $expectedSeconds = 200;
    $expectedLessonCount = 1;
    $expectedPercentComplete = 0;
    $tutorialWithStats = $this->tutorialRepository->getTutorialBySlugWithStats($slug, $personId);
    $this->assertSame($tutorialWithStats['title'], $title);
    $this->assertSame($tutorialWithStats['slug'], $slug);
    $this->assertSame($tutorialWithStats['thumbnail'], $updatedThumbnail);
    $this->assertEquals($tutorialWithStats['seconds'], $expectedSeconds);
    $this->assertEquals($tutorialWithStats['lesson_count'], $expectedLessonCount);
    $this->assertEquals($tutorialWithStats['percent_complete'], $expectedPercentComplete);

    // Test Captions
    $captionLanguageId = 1;
    $captionFilename = 'lesson1-english.vtt';
    $rowsInserted = $this->tutorialRepository->insertOrUpdateCaption(
      $lesson1,
      $captionLanguageId,
      $captionFilename
    );
    $this->assertEquals(1, $rowsInserted);
    $captionLanguages = $this->tutorialRepository->getCaptionLanguages();
    $this->assertEquals('English', $captionLanguages[0]['language']);
    $captionLanguage = $this->tutorialRepository->getCaptionLanguageById($captionLanguageId);
    $this->assertEquals('English', $captionLanguage['language']);
    $lessonCaptions = $this->tutorialRepository->getLessonCaptions($tutorialId1, $lessonInfo1['lesson_slug']);
    $expectedCaptions = [
      [
        'caption_filename' => $captionFilename,
        'language' => 'English',
        'srclang' => 'en'
      ]
    ];
    $this->assertEquals($expectedCaptions, $lessonCaptions);
  }
}
