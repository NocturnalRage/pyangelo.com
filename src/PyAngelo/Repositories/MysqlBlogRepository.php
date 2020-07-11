<?php
namespace PyAngelo\Repositories;

class MysqlBlogRepository implements BlogRepository {
  protected $dbh;

  public function __construct(\Mysqli $dbh) {
    $this->dbh = $dbh;
  }
  public function insertPublishedBlog($formData) {
    $sql = "INSERT INTO blog (
              blog_id,
              person_id,
              title,
              preview,
              content,
              slug,
              blog_image,
              blog_category_id,
              featured,
              published,
              published_at,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, 1, now(), now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'isssssii',
      $formData['person_id'],
      $formData['title'],
      $formData['preview'],
      $formData['content'],
      $formData['slug'],
      $formData['blog_image'],
      $formData['blog_category_id'],
      $formData['featured']
    );
    $stmt->execute();
    $blogId = $this->dbh->insert_id;
    $stmt->close();
    return $blogId;
  }

  public function updateBlogWithFormData($formData) {
    $sql = "UPDATE blog
            SET    title = ?,
                   featured = ?,
                   preview = ?,
                   content = ?,
                   blog_category_id = ?,
                   updated_at = now()
            WHERE  slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'sissis',
      $formData['title'],
      $formData['featured'],
      $formData['preview'],
      $formData['content'],
      $formData['blog_category_id'],
      $formData['slug']
    );
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function updateBlogImageBySlug($slug, $blogImage) {
    $sql = "UPDATE blog
            SET    blog_image = ?,
                   updated_at = now()
            WHERE  slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ss', $blogImage, $slug);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function getAllBlogs() {
    $sql = "SELECT b.*,
                   bc.description as category_description
            FROM   blog b
            JOIN   blog_category bc on b.blog_category_id = bc.blog_category_id
            ORDER BY b.published_at desc";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getFeaturedBlogs() {
    $sql = "SELECT b.*,
                   bc.description as category_description
            FROM   blog b
            JOIN   blog_category bc on b.blog_category_id = bc.blog_category_id
            WHERE  b.featured = 1
            ORDER BY b.published_at desc";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getBlogBySlug($slug) {
    $sql = "SELECT b.*,
                   bc.description as category_description
	        FROM   blog b
            JOIN   blog_category bc on b.blog_category_id = bc.blog_category_id
            WHERE  slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getAllBlogCategories() {
    $sql = "SELECT *
            FROM   blog_category";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getBlogCategoryById($blogCategoryId) {
    $sql = "SELECT *
	        FROM   blog_category
            WHERE  blog_category_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $blogCategoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getLatestImages() {
    $sql = "SELECT *
            FROM   blog_image
            ORDER BY created_at DESC";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function saveBlogImage($imageName, $width, $height) {
    $sql = "INSERT INTO blog_image (
              image_id,
              image_name,
              image_width,
              image_height,
              created_at
            )
            VALUES (NULL, ?, ?, ?, now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'sii',
      $imageName,
      $width,
      $height
    );
    $stmt->execute();
    $blogImageId = $this->dbh->insert_id;
    $stmt->close();
    return $blogImageId;
  }

  public function getPublishedBlogComments($blogId) {
    $sql = "SELECT p.person_id,
                   concat(p.given_name, ' ', p.family_name) as display_name,
                   p.email,
                   bc.*
            FROM   blog_comment bc
            JOIN   person p ON p.person_id = bc.person_id
            WHERE  bc.blog_id = ?
            AND    bc.published = 1
            ORDER BY bc.created_at";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $blogId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getLatestComments($offset, $limit) {
    $sql = "SELECT bc.comment_id, bc.blog_id, bc.person_id,
                   bc.blog_comment, bc.created_at,
                   b.title, b.slug,
                   p.person_id, p.email,
                   concat(p.given_name, ' ', p.family_name) as display_name
            FROM   blog_comment bc
            JOIN   blog b ON bc.blog_id = b.blog_id
            JOIN   person p ON bc.person_id = p.person_id
            WHERE  bc.published = TRUE
            ORDER BY bc.created_at DESC
            LIMIT ?, ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $offset, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getBlogById($blogId) {
    $sql = "SELECT *
	        FROM   blog
            WHERE  blog_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $blogId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getLatestBlogPost() {
    $sql = "SELECT b.*,
                   bc.description as category_description
            FROM   blog b
            JOIN   blog_category bc on b.blog_category_id = bc.blog_category_id
            ORDER BY published_at DESC
            LIMIT 1";
    $result = $this->dbh->query($sql);
    return $result->fetch_assoc();
  }

  public function insertBlogComment($commentData) {
    $sql = "INSERT INTO blog_comment (
              comment_id,
              blog_id,
              person_id,
              blog_comment,
              published,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, ?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'iisi',
      $commentData['blog_id'],
      $commentData['person_id'],
      $commentData['blog_comment'],
      $commentData['published']
    );
    $stmt->execute();
    $commentId = $this->dbh->insert_id;
    $stmt->close();
    return $commentId;
  }

  public function unpublishCommentById($commentId) {
    $sql = "UPDATE blog_comment
            SET    published = 0
            WHERE  comment_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $commentId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function getAllLivestreams() {
    $sql = "SELECT *
            FROM   livestream
            ORDER BY created_at desc";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function insertLivestream($formData) {
    $sql = "INSERT INTO livestream (
              livestream_id,
              livestream_title,
              livestream_description,
              video_name,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'sss',
      $formData['livestream_title'],
      $formData['livestream_description'],
      $formData['video_name']
    );
    $stmt->execute();
    $livestreamId = $this->dbh->insert_id;
    $stmt->close();
    return $livestreamId;
  }

  public function updateLivestreamById($formData) {
    $sql = "UPDATE livestream
            SET    livestream_title = ?,
                   livestream_description = ?,
                   video_name = ?,
                   updated_at = now()
            WHERE  livestream_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'sssi',
      $formData['livestream_title'],
      $formData['livestream_description'],
      $formData['video_name'],
      $formData['livestream_id']
    );
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function getLivestreamById($livestreamId) {
    $sql = "SELECT *
	        FROM   livestream
            WHERE  livestream_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $livestreamId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getLivestreamChat() {
    $sql = "SELECT *
            FROM   livestream_chat";
    $result = $this->dbh->query($sql);
    return $result->fetch_assoc();
  }

  public function updateLivestreamChat($chatId) {
    $sql = "UPDATE livestream_chat
            SET    livestream_chat_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $chatId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function updateLivestreamPosterById($livestreamId, $poster) {
    $sql = "UPDATE livestream
            SET    poster = ?,
                   updated_at = now()
            WHERE  livestream_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('si', $poster, $livestreamId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function getHomepageContent() {
    $sql = "SELECT *
	        FROM   homepage_content";
    $result = $this->dbh->query($sql);
    return $result->fetch_assoc();
  }

  public function updateHomepageContent($recentlyReleased, $comingSoon) {
    $sql = "UPDATE homepage_content
            SET    recently_released = ?,
                   coming_soon = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ss', $recentlyReleased, $comingSoon);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function addToBlogAlert($blogId, $personId) {
    $sql = "INSERT INTO blog_alert (blog_id, person_id, created_at, updated_at)
            VALUES (?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $blogId, $personId);
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function removeFromBlogAlert($blogId, $personId) {
    $sql = "DELETE FROM blog_alert
            WHERE  blog_id = ?
            AND    person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $blogId, $personId);
    $stmt->execute();
    $rowsDeleted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsDeleted;
  }

  public function shouldUserReceiveAlert($blogId, $personId) {
    $sql = "SELECT blog_id
	        FROM   blog_alert
            WHERE  blog_id = ?
            AND    person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $blogId, $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getFollowers($blogId) {
    $sql = "SELECT person_id
            FROM   blog_alert
            WHERE  blog_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $blogId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }
}
