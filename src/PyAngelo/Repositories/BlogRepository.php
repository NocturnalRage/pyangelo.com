<?php
namespace PyAngelo\Repositories;

interface BlogRepository {

  public function insertPublishedBlog($formData);

  public function updateBlogWithFormData($formData);

  public function updateBlogImageBySlug($slug, $blogImage);

  public function getAllBlogs();

  public function getFeaturedBlogs();

  public function getBlogBySlug($slug);

  public function getBlogById($blogId);

  public function getLatestBlogPost();

  public function getAllBlogCategories();

  public function getBlogCategoryById($blogCategoryId);

  public function getLatestImages();

  public function saveBlogImage($imageName, $width, $height);

  public function getPublishedBlogComments($blogId);

  public function getLatestComments($offset, $limit);

  public function insertBlogComment($commentData);

  public function unpublishCommentById($commentId);

  public function getAllLivestreams();

  public function insertLivestream($formData);

  public function updateLivestreamById($formData);

  public function getLivestreamById($livestreamId);

  public function getLivestreamChat();

  public function updateLivestreamChat($chatId);

  public function updateLivestreamPosterById($livestreamId, $poster);

  public function getHomepageContent();

  public function updateHomepageContent($recentlyReleased, $comingSoon);

  public function addToBlogAlert($blogId, $personId);

  public function removeFromBlogAlert($blogId, $personId);

  public function shouldUserReceiveAlert($blogId, $personId);

  public function getFollowers($blogId);

}
