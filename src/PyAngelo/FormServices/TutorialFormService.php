<?php
namespace PyAngelo\FormServices;

use PyAngelo\Auth\Auth;
use PyAngelo\Repositories\TutorialRepository;

class TutorialFormService {
  protected $errors = [];
  protected $flashMessage;

  protected $auth;
  protected $tutorialRepository;

  public function __construct(
    Auth $auth,
    TutorialRepository $tutorialRepository
  ) {
    $this->auth = $auth;
    $this->tutorialRepository = $tutorialRepository;
  }

  public function createTutorial($formData, $imageInfo, $pdfInfo = NULL) {
    if (!$this->isFormDataValid($formData)) {
      $this->flashMessage = 'There were some errors. ' .
         'Please fix these and then we will create the tutorial. You will also need to re-select the thumbnail for this tutorial series.';
      return false;
    }
    if (!$this->isThumbnailValid($imageInfo)) {
      $this->flashMessage = 'The thumbnail was not valid. ' .
         'Please select another thumbnail image and we will create the tutorial.';
      return false;
    }

    $slug = $this->generateSlug($formData['title']);

    $thumbnail = $this->moveFile('uploads/images/tutorials', $imageInfo, $slug);
    if (!$thumbnail) {
      return false;
    }
    $formData['slug'] = $slug;
    $formData['thumbnail'] = $thumbnail;

    if (empty($formData['tutorial_sketch_id'])) {
      $formData['tutorial_sketch_id'] = null;
    }

    $tutorialId = $this->insertTutorial($formData);

    if ($pdfInfo['size'] > 0) {
      if (!$this->isPdfValid($pdfInfo)) {
        $this->flashMessage = 'The PDF was not valid. ' .
           'Please select another PDF and we will update the tutorial.';
        return false;
      }
      $pdf = $this->moveFile('uploads/pdf/tutorials', $pdfInfo, $slug);
      if (!$pdf) {
        $this->flashMessage = 'The PDF could not be uploaded.';
        return false;
      }
      $rowsUpdated = $this->tutorialRepository->updateTutorialPdfBySlug(
        $formData['slug'],
        $pdf
      );
    }
    return $slug;
  }

  public function updateTutorial($formData, $imageInfo = NULL, $pdfInfo = NULL) {
    if (!isset($formData['slug'])) {
      $this->flashMessage = "Sorry, something has gone wrong. Let's start again.";
      return false;
    }
    if (!($tutorial = $this->tutorialRepository->getTutorialBySlug($formData['slug']))) {
      $this->flashMessage = "Sorry, something has gone wrong. Let's start again.";
      return false;
    }
    if (!$this->isFormDataValid($formData)) {
      $this->flashMessage = 'There were some errors. ' .
         'Please fix these and then we will update the tutorial. You will also need to re-select the thumbnail for this tutorial series if you changed it.';
      return false;
    }
    if (! empty($imageInfo) && $imageInfo['size'] > 0) {
      if (!$this->isThumbnailValid($imageInfo)) {
        $this->flashMessage = 'The thumbnail was not valid. ' .
           'Please select another thumbnail image and we will update the tutorial.';
        return false;
      }
      $thumbnail = $this->moveFile('uploads/images/tutorials', $imageInfo, $formData['slug']);
      if (!$thumbnail) {
        $this->flashMessage = 'The thumbnail could not be uploaded.';
        return false;
      }
      $formData['thumbnail'] = $thumbnail;
      $rowsUpdated = $this->tutorialRepository->updateTutorialThumbnailBySlug(
        $formData['slug'],
        $formData['thumbnail']
      );
    }
    if (! empty($pdfInfo) && $pdfInfo['size'] > 0) {
      if (!$this->isPdfValid($pdfInfo)) {
        $this->flashMessage = 'The PDF was not valid. ' .
           'Please select another PDF and we will update the tutorial.';
        return false;
      }
      $pdf = $this->moveFile('uploads/pdf/tutorials', $pdfInfo, $formData['slug']);
      if (!$pdf) {
        $this->flashMessage = 'The PDF could not be uploaded.';
        return false;
      }
      $rowsUpdated = $this->tutorialRepository->updateTutorialPdfBySlug(
        $formData['slug'],
        $pdf
      );
    }
    $rowsUpdated = $this->tutorialRepository->updateTutorialBySlug(
      $formData['slug'],
      $formData['title'],
      $formData['description'],
      $formData['tutorial_category_id'],
      $formData['tutorial_level_id'],
      $formData['single_sketch'],
      $formData['tutorial_sketch_id'],
      $formData['display_order']
    );
    return true;
  }

  public function getErrors() {
    return $this->errors;
  }

  public function getFlashMessage() {
    return $this->flashMessage;
  }

  private function insertTutorial($formData) {
    return $this->tutorialRepository->insertTutorial(
      $formData['title'],
      $formData['description'],
      $formData['slug'],
      $formData['tutorial_category_id'],
      $formData['tutorial_level_id'],
      $formData['single_sketch'],
      $formData['tutorial_sketch_id'],
      $formData['display_order'],
      $formData['thumbnail']
    );
  }

  private function generateSlug($title) {
    $slug = substr($title, 0, 100);
    $slug = strtolower($slug);
    $slug = str_replace('.', '-', $slug);
    $slug = preg_replace('/[^a-z0-9 ]/', '', $slug);
    $slug = preg_replace('/\s+/', '-', $slug);
    $slug = trim($slug, '-');
    $slugVersion = 0;
    while ($this->tutorialRepository->getTutorialBySlug($slug)) {
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

  private function isFormDataValid($formData) {
    if (!$this->auth->crsfTokenIsValid()) {
      $this->errors['crsfToken'] = "Invalid CRSF token.";
    }
    if (empty($formData['title'])) {
      $this->errors['title'] = "You must supply a title for this tutorial series.";
    }
    else if (strlen($formData['title']) > 100) {
      $this->errors["title"] = "The title can be no longer than 100 characters.";
    }
    else {
      $tutorial = $this->tutorialRepository->getTutorialByTitle($formData['title']);
      if ($tutorial && $tutorial['slug'] != ($formData['slug'] ?? '')) {
        $this->errors["title"] = "The title is already used by another tutorial.";
      }
    }

    if (empty($formData['description'])) {
      $this->errors['description'] = "The description field cannot be blank.";
    }
    elseif (strlen($formData['description']) > 1000) {
      $this->errors["description"] = "The description can be no longer than 1000 characters.";
    }

    if (empty($formData['tutorial_category_id'])) {
      $this->errors['tutorial_category_id'] = "You must select the category this tutorial belongs to.";
    }
    else if (! $this->tutorialRepository->getTutorialCategoryById($formData['tutorial_category_id'])) {
      $this->errors['tutorial_category_id'] = "The specified category for this tutorial does not exist.";
    }

    if (empty($formData['tutorial_level_id'])) {
      $this->errors['tutorial_level_id'] = "You must select the level of this tutorial.";
    }
    else if (! $this->tutorialRepository->getTutorialLevelById($formData['tutorial_level_id'])) {
      $this->errors['tutorial_level_id'] = "The specified level for this tutorial does not exist.";
    }

    if (! isset($formData['single_sketch'])) {
      $this->errors['single_sketch'] = "You must select if there will only be a single sketch for the entire tutorial.";
    }
    else if ($formData['single_sketch'] != 0 && $formData['single_sketch'] != 1) {
      $this->errors['single_sketch'] = "You must select either true or false.";
    }
    else if ($formData['single_sketch'] == 1) {
      if (empty($formData['tutorial_sketch_id'])) {
        $this->errors['tutorial_sketch_id'] = "As this tutorial has a single sketch you must select such a sketch to be cloned by users.";
      }
      // TODO: Should check this is a valid sketch ID
    }

    if (empty($formData['display_order'])) {
      $this->errors['display_order'] = "You must select where this will be displayed relative to other tutorial series.";
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

  private function isThumbnailValid($imageInfo) {
    if ($imageInfo["size"] == 0) {
      $this->errors["thumbnail"] = "You must select an image for this tutorial series.";
    }
    if ($imageInfo['size'] > 1048576) {
      $this->errors["thumbnail"] = "Sorry, the file is larger than 1MB. Please reduce the size and try again.";
    }
    elseif ($imageInfo['type'] != 'image/jpeg' && $imageInfo['type'] != 'image/png') {
      $this->errors["thumbnail"] = "Could not process an image of type " . $imageInfo['type'] . ". The image must be a .jpg, or .png";
    }
    if (!empty($this->errors)) {
      return false;
    }
    return true;
  }

  private function isPdfValid($pdfInfo) {
    // This is not mandatory, so if there is no pdf it is valid.
    if ($pdfInfo["size"] == 0) {
      return true;
    }
    if ($pdfInfo['size'] > 10485760) {
      $this->errors["pdf"] = "Sorry, the PDF file is larger than 10MB. Please reduce the size and try again.";
    }
    elseif ($pdfInfo['type'] != 'application/pdf') {
      $this->errors["thumbnail"] = "You need to upload a document with a .pdf extension. This had a type of " . $pdfInfo['type'] . ".";
    }
    if (!empty($this->errors)) {
      return false;
    }
    return true;
  }
}
