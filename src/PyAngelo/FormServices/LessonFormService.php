<?php
namespace PyAngelo\FormServices;

use PyAngelo\Auth\Auth;
use PyAngelo\Repositories\TutorialRepository;

class LessonFormService {
  protected $errors = [];
  protected $flashMessage;

  protected $tutorialRepository;

  public function __construct(
    TutorialRepository $tutorialRepository
  ) {
    $this->tutorialRepository = $tutorialRepository;
  }

  public function createLesson($formData, $imageInfo = NULL) {
    $tutorial = [];
    if (empty($formData['slug'])) {
      $this->errors['tutorial_id'] = "This lesson must be part of a tutorial series.";
    }
    else if (! $tutorial = $this->tutorialRepository->getTutorialBySlug($formData['slug'])) {
      $this->errors['tutorial_id'] = "This lesson must be part of a tutorial series.";
    }
    else {
      $formData['tutorial_id'] = $tutorial['tutorial_id'];
    }

    if (!$this->isFormDataValid($formData, $tutorial)) {
      $this->flashMessage = 'There were some errors. ' .
         'Please fix these and then we will create the lesson.';
      return false;
    }

    if (empty($formData['lesson_sketch_id'])) {
      $formData['lesson_sketch_id'] = null;
    }
    $formData['lesson_slug'] = $this->generateSlug(
      $formData['lesson_title'],
      $formData['tutorial_id']
    );
    $lessonId = $this->tutorialRepository->insertLesson($formData);

    return $this->saveAndMovePoster(
      $imageInfo,
      $formData['tutorial_id'],
      $formData['lesson_slug']
    );
  }

  public function updateLesson($formData, $imageInfo = NULL) {
    if (! isset($formData['slug']) ||
        ! isset($formData['lesson_slug'])
    ) {
      $this->flashMessage = "Sorry, something has gone wrong. Let's start again.";
      return false;
    }

    if (! $tutorial = $this->tutorialRepository->getTutorialBySlug($formData['slug'])) {
      $this->errors['tutorial_id'] = "This lesson must be part of a tutorial series.";
    }
    else {
      $formData['tutorial_id'] = $tutorial['tutorial_id'];
    }

    if (!($lesson = $this->tutorialRepository->getLessonBySlugs(
      $formData['slug'],
      $formData['lesson_slug']
    ))) {
      $this->flashMessage = "Sorry, something has gone wrong. Let's start again.";
      return false;
    }
    if (!$this->isFormDataValid($formData, $tutorial)) {
      $this->flashMessage = 'There were some errors. ' .
         'Please fix these and then we will update the lesson.'; 
      return false;
    }

    if (empty($formData['lesson_sketch_id'])) {
      $formData['lesson_sketch_id'] = null;
    }

    $rowsUpdated = $this->tutorialRepository->updateLessonByTutorialIdAndSlug($formData);

    return $this->saveAndMovePoster(
      $imageInfo,
      $formData['tutorial_id'],
      $formData['lesson_slug']
    );
  }

  public function getErrors() {
    return $this->errors;
  }

  public function getFlashMessage() {
    return $this->flashMessage;
  }

  private function generateSlug($title, $tutorialId) {
    $slug = substr($title, 0, 100);
    $slug = strtolower($slug);
    $slug = str_replace('.', '-', $slug);
    $slug = preg_replace('/[^a-z0-9 ]/', '', $slug);
    $slug = preg_replace('/\s+/', '-', $slug);
    $slug = trim($slug, '-');
    $slugVersion = 0;
    while ($this->tutorialRepository->getLessonBySlugAndTutorialId($slug, $tutorialId)) {
      $slugVersion++;
      $slug = $slug . '-' . $slugVersion;
    }
    return $slug;
  }

  private function moveFile($baseDir, $fileInfo, $fileName) {
    $imageFileType = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
    $fullFileName = $fileName . '.' . $imageFileType;
    $uploadFile = $baseDir . '/' . $fullFileName;

    if (!move_uploaded_file($fileInfo['tmp_name'], $uploadFile)) {
      return false;
    }
    return $fullFileName;
  }

  private function isFormDataValid($formData, $tutorial) {
    if (empty($formData['tutorial_id'])) {
      $this->errors['tutorial_id'] = "This lesson must be part of a tutorial series.";
    }

    if (empty($formData['lesson_title'])) {
      $this->errors['lesson_title'] = "You must supply a title for this lesson.";
    }
    else if (strlen($formData['lesson_title']) > 100) {
      $this->errors["lesson_title"] = "The title of this lesson can be no longer than 100 characters.";
    }
    else {
      $lesson = $this->tutorialRepository->getLessonByTitleAndTutorialId($formData['lesson_title'], $formData['tutorial_id']);
      if ($lesson && $lesson['lesson_slug'] != ($formData['lesson_slug'] ?? 'NewLesson')) {
        $this->errors["lesson_title"] = "The title is already used by another lesson within this tutorial.";
      }
    }

    if (empty($formData['lesson_description'])) {
      $this->errors['lesson_description'] = "The lesson description cannot be blank.";
    }
    elseif (strlen($formData['lesson_description']) > 1000) {
      $this->errors["lesson_description"] = "The lesson description can be no longer than 1000 characters.";
    }

    if (empty($formData['video_name'])) {
      $this->errors['video_name'] = "You must supply a video name.";
    }
    elseif (strlen($formData['video_name']) > 100) {
      $this->errors["video_name"] = "The video name can be no longer than 100 characters.";
    }

    if (! empty($formData['youtube_url'])) {
      if (strlen($formData['youtube_url']) > 255) {
        $this->errors["youtube_url"] = "The YouTube URL can be no longer than 255 characters.";
      }
    }

    if (empty($formData['seconds'])) {
      $this->errors['seconds'] = "You must record the duration of this lesson in seconds.";
    }
    else if (!(is_numeric($formData['seconds']))) {
      $this->errors["title"] = "Seconds must be a number.";
    }
    else if ($formData['seconds'] < 1 || $formData['seconds'] > 9999) {
      $this->errors["seconds"] = "The duration of the lesson must be between 1 and 9999 seconds.";
    }

    if (empty($formData['lesson_security_level_id'])) {
      $this->errors['lesson_security_level_id'] = "You must select the security level for this lesson.";
    }
    else if (! $this->tutorialRepository->getLessonSecurityLevelById($formData['lesson_security_level_id'])) {
      $this->errors['lesson_security_level_id'] = "The specified security level for this lesson does not exist.";
    }

    if (isset($tutorial['single_sketch']) && ! $tutorial['single_sketch']) {
      if (empty($formData['lesson_sketch_id'])) {
        $this->errors['lesson_sketch_id'] = "You must choose a sketch to clone for the users for this lesson.";
      }
      // TODO: Check sketch exists!
    }

    if (empty($formData['display_order'])) {
      $this->errors['display_order'] = "You must select where this will be displayed relative to other lessons in this tutorial series.";
    }
    else if (!(is_numeric($formData['display_order']))) {
      $this->errors["title"] = "The display order must be a number.";
    }
    else if ($formData['display_order'] < 1 || $formData['display_order'] > 999) {
      $this->errors["display_order"] = "The display order must be between 1 and 999.";
    }

    if (!empty($this->errors)) {
      return false;
    }
    return true;
  }

  private function isPosterValid($imageInfo) {
    // This is not mandatory, so if there is no image it is valid.
    if ($imageInfo["size"] == 0) {
      return true;
    }
    if ($imageInfo['size'] > 1048576) {
      $this->errors["poster"] = "Sorry, the file is larger than 1MB. Please reduce the size and try again.";
    }
    elseif ($imageInfo['type'] != 'image/jpeg' && $imageInfo['type'] != 'image/png') {
      $this->errors["poster"] = "Could not process an image of type " . $imageInfo['type'] . ". The image must be a .jpg, or .png";
    }
    if (!empty($this->errors)) {
      return false;
    }
    return true;
  }

  private function saveAndMovePoster($imageInfo, $tutorialId, $lessonSlug) {
    if (! empty($imageInfo) && $imageInfo['size'] > 0) {
      if (!$this->isPosterValid($imageInfo)) {
        $this->flashMessage = 'The poster image was not valid. ' .
           'Please select another image and we will update the lesson.';
        return false;
      }
      $poster = $this->moveFile(
        'uploads/images/lessons',
        $imageInfo,
        $lessonSlug . '-' . $tutorialId
      );
      if (!$poster) {
        $this->flashMessage = 'The poster image could not be uploaded.';
        return false;
      }
      $rowsUpdated = $this->tutorialRepository->updateLessonPosterByTutorialIdAndSlug(
        $tutorialId,
        $lessonSlug,
        $poster
      );
    }
    return true;
  }
}
