<?php
namespace PyAngelo\FormServices;

use PyAngelo\Auth\Auth;
use PyAngelo\Repositories\BlogRepository;

class BlogFormService {
  protected $errors = [];
  protected $flashMessage;

  protected $auth;
  protected $blogRepository;

  public function __construct(
    Auth $auth,
    BlogRepository $blogRepository
  ) {
    $this->auth = $auth;
    $this->blogRepository = $blogRepository;
  }

  public function createBlog($formData, $imageInfo) {
    if (!$this->isFormDataValid($formData)) {
      $this->flashMessage = 'There were some errors. ' .
         'Please fix these and then we will create the blog. You will also need to re-select the image for this blog.';
      return false;
    }
    if (!$this->isBlogImageValid($imageInfo)) {
      $this->flashMessage = 'The image was not valid. ' .
         'Please select another image and we will create the blog.';
      return false;
    }

    $formData['slug'] = $this->generateSlug($formData['title']);
    $formData['featured'] = $formData['featured'] ?? 0;
    $formData['person_id'] = $this->auth->personId();

    $blogImage = $this->moveFile(
      'uploads/images/blog_thumbnail',
      $imageInfo,
      $formData['slug']
    );
    if (!$blogImage) {
      return false;
    }

    $formData['blog_image'] = $blogImage;

    $blogId = $this->blogRepository->insertPublishedBlog($formData);

    return $formData['slug'];
  }

  public function updateBlog($formData, $imageInfo = NULL) {
    if (!isset($formData['slug'])) {
      $this->flashMessage = "Sorry, something has gone wrong. Let's start again.";
      return false;
    }
    if (!($blog = $this->blogRepository->getBlogBySlug($formData['slug']))) {
      $this->flashMessage = "Sorry, something has gone wrong. Let's start again.";
      return false;
    }
    if (!$this->isFormDataValid($formData)) {
      $this->flashMessage = 'There were some errors. ' .
         'Please fix these and then we will update the blog. If you changed the blog image you will need to re-select your image.';
      return false;
    }
    if (! empty($imageInfo) && $imageInfo['size'] > 0) {
      if (!$this->isBlogImageValid($imageInfo)) {
        $this->flashMessage = 'The image was not valid. ' .
           'Please select another image and we will update the blog.';
        return false;
      }
      $blogImage = $this->moveFile('uploads/images/blog_thumbnail', $imageInfo, $formData['slug']);
      if (!$blogImage) {
        $this->flashMessage = 'The image could not be uploaded.';
        return false;
      }
      $formData['blogImage'] = $blogImage;
      $rowsUpdated = $this->blogRepository->updateBlogImageBySlug(
        $formData['slug'],
        $formData['blogImage']
      );
    }
    $formData['featured'] = $formData['featured'] ?? 0;
    $rowsUpdated = $this->blogRepository->updateBlogWithFormData($formData);
    return true;
  }

  public function getErrors() {
    return $this->errors;
  }

  public function getFlashMessage() {
    return $this->flashMessage;
  }

  private function generateSlug($title) {
    $slug = substr($title, 0, 100);
    $slug = strtolower($slug);
    $slug = str_replace('.', '-', $slug);
    $slug = preg_replace('/[^a-z0-9 ]/', '', $slug);
    $slug = preg_replace('/\s+/', '-', $slug);
    $slug = trim($slug, '-');
    if ($blog = $this->blogRepository->getBlogBySlug($slug)) {
      $slug = $slug . '-' . date('Y-m-d');
      $slugVersion = 0;
      while ($this->blogRepository->getBlogBySlug($slug)) {
        $slugVersion++;
        $slug = $slug . '-' . $slugVersion;
      }
    }
    return $slug;
  }

  private function isFormDataValid($formData) {
    if (!$this->auth->crsfTokenIsValid()) {
      $this->errors['crsfToken'] = "Invalid CRSF token.";
    }
    if (empty($formData['title'])) {
      $this->errors['title'] = "You must supply a title for this blog.";
    }
    else if (strlen($formData['title']) > 100) {
      $this->errors["title"] = "The title can be no longer than 100 characters.";
    }

    if (empty($formData['content'])) {
      $this->errors['content'] = "The content field cannot be blank.";
    }

    if (empty($formData['preview'])) {
      $this->errors['preview'] = "The preview field cannot be blank.";
    }
    else if (strlen($formData['preview']) > 1000) {
      $this->errors["preview"] = "The preview can be no longer than 1000 characters.";
    }

    if (empty($formData['blog_category_id'])) {
      $this->errors['blog_category_id'] = "You must select the category this blog belongs to.";
    }
    else if (! $this->blogRepository->getBlogCategoryById($formData['blog_category_id'])) {
      $this->errors['blog_category_id'] = "The specified category for this blog does not exist.";
    }

    if (! empty($this->errors)) {
      return false;
    }
    return true;
  }

  private function isBlogImageValid($imageInfo) {
    if ($imageInfo["size"] == 0) {
      $this->errors["blog_image"] = "You must select an image for this blog.";
    }
    if ($imageInfo['size'] > 1048576) {
      $this->errors["blog_image"] = "Sorry, the file is larger than 1MB. Please reduce the size and try again.";
    }
    elseif ($imageInfo['type'] != 'image/jpeg' && $imageInfo['type'] != 'image/png') {
      $this->errors["blog_image"] = "Could not process an image of type " . $imageInfo['type'] . ". The image must be a .jpg, or .png";
    }
    if (!empty($this->errors)) {
      return false;
    }
    return true;
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
}
