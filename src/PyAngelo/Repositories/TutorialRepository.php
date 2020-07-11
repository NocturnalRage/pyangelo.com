<?php
namespace PyAngelo\Repositories;

interface TutorialRepository {

  public function getAllTutorials();

  public function getTutorialsByCategory($slug);

  public function getTutorialBySlug($slug);

  public function getTutorialBySlugWithStats($slug, $personId);

  public function getTutorialPercentComplete($personId, $lessonId);

  public function getTutorialByTitle($title);

  public function insertTutorial(
    $title,
    $description,
    $slug,
    $tutorialCategoryId,
    $tutorialLevelId,
    $singleSketch,
    $displayOrder,
    $thumbnail
  );

  public function updateTutorialBySlug(
    $slug,
    $title,
    $description,
    $tutorialCategoryId,
    $tutorialLevelId,
    $singleSketch,
    $displayOrder
  );

  public function updateTutorialOrder($slug, $position);

  public function updateLessonOrder($tutorialId, $lessonSlug, $position);

  public function updateTutorialThumbnailBySlug($slug, $thumbnail);

  public function updateTutorialPdfBySlug($slug, $pdf);

  public function updateLessonPosterByTutorialIdAndSlug(
    $tutorialId,
    $lessonSlug,
    $poster
  );

  public function insertTutorialCategory($id, $category, $slug, $displayOrder);

  public function insertTutorialLevel($id, $description);

  public function getAllTutorialCategories();

  public function getAllTutorialLevels();

  public function getTutorialCategoryById($categoryId);

  public function getTutorialLevelById($levelId);

  public function insertLessonSecurityLevel($id, $description);

  public function getLessonSecurityLevelById($levelId);

  public function getAllLessonSecurityLevels();

  public function insertLesson($lessonInfo);

  public function getLessonById($lessonId);

  public function getLessonByTitleAndTutorialId($title, $tutorialId);

  public function insertLessonCompleted($personId, $lessonId);

  public function insertLessonFavourited($personId, $lessonId);

  public function deleteLessonCompleted($personId, $lessonId);

  public function deleteLessonFavourited($personId, $lessonId);

  public function getLessonCompleted($personId, $lessonId);

  public function getLessonFavourited($personId, $lessonId);

  public function getAllFavourites($personId);

  public function getTutorialLessons($tutorialId, $personId);

  public function getLessonBySlugAndTutorialId($lessonSlug, $tutorialId);

  public function getLessonBySlugs($tutorialSlug, $lessonSlug);

  public function getLessonBySlugsWithStatus(
    $tutorialSlug,
    $lessonSlug,
    $personId
  );

  public function getLessonCaptions($tutorialId, $lessonSlug);

  public function getCaptionLanguages();

  public function getCaptionLanguageById($captionLanguageId);

  public function insertOrUpdateCaption(
    $lessonId,
    $captionLanguageId,
    $captionFilename
  );

  public function updateLessonByTutorialIdAndSlug($formData);

  public function getNextLessonInTutorial($tutorialId, $displayOrder);

  public function insertLessonComment($commentData);
 
  public function getPublishedLessonComments($lessonId);

  public function getLatestComments($offset, $limit);

  public function unpublishCommentById($commentId);

  public function deleteAllTutorials();

  public function deleteAllTutorialCategories();

  public function deleteAllTutorialLevels();

  public function deleteAllLessons();

  public function deleteAllLessonSecurityLevels();

  public function addToLessonAlert($lessonId, $personId);

  public function removeFromLessonAlert($lessonId, $personId);

  public function shouldUserReceiveAlert($lessonId, $personId);

  public function getFollowers($lessonId);
}
