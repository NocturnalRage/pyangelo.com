<?php
namespace PyAngelo\Repositories;

class MysqlQuestionRepository implements QuestionRepository {
  protected $dbh;

  public function __construct(\Mysqli $dbh) {
    $this->dbh = $dbh;
  }

  public function getLatestQuestions($offset, $limit) {
    $sql = "SELECT q.question_id, q.person_id, q.question_title,
                   q.teacher_id, q.created_at, q.answered_at, q.updated_at,
                   q.published, q.question_type_id,
                   qt.description question_category,
                   qt.category_slug,
                   q.slug
            FROM   question q
            JOIN   question_type qt on q.question_type_id = qt.question_type_id
            WHERE  published = TRUE
            ORDER BY updated_at DESC
            LIMIT ?, ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $offset, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getLatestComments($offset, $limit) {
    $sql = "SELECT qc.comment_id, qc.question_id,
                   qc.question_comment, qc.created_at,
                   q.question_title, q.slug,
                   p.person_id, p.email, p.admin,
                   concat(p.given_name, ' ', p.family_name) as display_name
            FROM   question_comment qc
            JOIN   question q ON qc.question_id = q.question_id
            JOIN   person p ON qc.person_id = p.person_id
            WHERE  qc.published = TRUE
            ORDER BY qc.created_at DESC
            LIMIT ?, ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $offset, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getUnansweredQuestions() {
    $sql = "SELECT q.*,
                   qt.description as category_description,
                   p.email as questionee_email,
                   concat(p.given_name, ' ', p.family_name) as display_name,
                   c.email as teacher_email,
                   concat(c.given_name, ' ', c.family_name) as teacher_display_name
	        FROM   question q
            JOIN   person p ON p.person_id = q.person_id
            JOIN   person c ON c.person_id = q.teacher_id
            JOIN   question_type qt on q.question_type_id = qt.question_type_id
            WHERE  q.published = 0
            AND    q.answered_at is NULL
            ORDER by q.created_at";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getQuestionsByPersonId($personId) {
    $sql = "SELECT q.question_id, q.person_id, q.question_title,
                   q.teacher_id, q.created_at, q.answered_at, q.updated_at,
                   q.published, q.question_type_id,
                   qt.description question_category,
                   qt.category_slug,
                   q.slug
            FROM   question q
            JOIN   question_type qt on q.question_type_id = qt.question_type_id
            WHERE  q.person_id = ?
            ORDER BY created_at DESC";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getQuestionById($questionId) {
    $sql = "SELECT q.*
	        FROM   question q
            WHERE  q.question_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $questionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getQuestionBySlug($slug) {
    $sql = "SELECT q.*,
                   qt.description as category_description,
                   p.email as questionee_email,
                   concat(p.given_name, ' ', p.family_name) as display_name,
                   c.email as teacher_email,
                   concat(c.given_name, ' ', c.family_name) as teacher_display_name
	        FROM   question q
            JOIN   person p ON p.person_id = q.person_id
            JOIN   person c ON c.person_id = q.teacher_id
            JOIN   question_type qt on q.question_type_id = qt.question_type_id
            WHERE  q.slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getQuestionBySlugWithStatus($slug, $personId) {
    $sql = "SELECT q.*,
                   CASE WHEN qf.favourited_at IS NULL THEN 0 ELSE 1 END AS favourited,
                   qt.description as category_description,
                   p.email as questionee_email,
                   concat(p.given_name, ' ', p.family_name) as display_name,
                   c.email as teacher_email,
                   concat(c.given_name, ' ', c.family_name) as teacher_display_name
	        FROM   question q
            JOIN   person p ON p.person_id = q.person_id
            JOIN   person c ON c.person_id = q.teacher_id
            JOIN   question_type qt on q.question_type_id = qt.question_type_id
            LEFT JOIN question_favourited qf on qf.question_id = q.question_id AND qf.person_id = ?
            WHERE  q.slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('is', $personId, $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getPublishedQuestionComments($questionId) {
    $sql = "SELECT p.person_id,
                   concat(p.given_name, ' ', p.family_name) as display_name,
                   p.email,
                   p.admin,
                   qc.*
            FROM   question_comment qc
            JOIN   person p ON p.person_id = qc.person_id
            WHERE  qc.question_id = ?
            AND    qc.published = 1
            ORDER BY qc.created_at";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $questionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getNextQuestion($dateUpdated) {
    $sql = "SELECT q.question_id, q.slug, q.question_title
            FROM   question q
            WHERE  q.published = TRUE
            AND    q.updated_at > ?
            ORDER BY q.updated_at
            LIMIT 1";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $dateUpdated);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getPreviousQuestion($dateUpdated) {
    $sql = "SELECT q.question_id, q.slug, q.question_title
            FROM   question q
            WHERE  q.published = TRUE
            AND    q.updated_at < ?
            ORDER BY q.updated_at DESC
            LIMIT 1";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $dateUpdated);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getCategoryBySlug($slug) {
    $sql = "SELECT *
            FROM   question_type
            WHERE  category_slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getCategoryQuestionsBySlug($slug) {
    $sql = "SELECT q.question_id, q.person_id, q.question_title,
                   q.teacher_id, q.created_at, q.answered_at, q.updated_at,
                   q.published, q.question_type_id,
                   qt.description question_category,
                   qt.category_slug,
                   q.slug
            FROM   question q
            JOIN   question_type qt on q.question_type_id = qt.question_type_id
            WHERE  published = TRUE
            AND    qt.category_slug = ?
            ORDER BY updated_at DESC";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function insertQuestionComment($commentData) {
    $sql = "INSERT INTO question_comment (
              comment_id,
              question_id,
              person_id,
              question_comment,
              published,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, ?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'iisi',
      $commentData['question_id'],
      $commentData['person_id'],
      $commentData['question_comment'],
      $commentData['published']
    );
    $stmt->execute();
    $commentId = $this->dbh->insert_id;
    $stmt->close();
    return $commentId;
  }

  public function updateQuestionLastUpdatedDate($questionId) {
    $sql = "UPDATE question
            SET    updated_at = now()
            WHERE  question_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $questionId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function unpublishCommentById($commentId) {
    $sql = "UPDATE question_comment
            SET    published = 0
            WHERE  comment_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $commentId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function createQuestion(
    $personId,
    $questionTitle,
    $question,
    $slug
  ) {
    $sql = "INSERT INTO question (
              question_id,
              person_id,
              question_title,
              question,
              teacher_id,
              created_at,
              updated_at,
              published,
              question_type_id,
              slug
            )
            VALUES (NULL, ?, ?, ?, 1, now(), now(), 0, 1, ?)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'isss',
      $personId,
      $questionTitle,
      $question,
      $slug
    );
    $stmt->execute();
    $questionId = $this->dbh->insert_id;
    $stmt->close();
    return $questionId;
  }

  public function answerQuestion(
    $questionId,
    $questionTitle,
    $question,
    $answer,
    $questionTypeId,
    $teacherId,
    $slug,
    $answeredAt
  ) {
    $sql = "UPDATE question
            SET    question_title = ?,
                   question = ?,
                   answer = ?,
                   question_type_id = ?,
                   teacher_id = ?,
                   slug = ?,
                   answered_at = ?,
                   updated_at = now(),
                   published = 1
            WHERE  question_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'sssiissi',
      $questionTitle,
      $question,
      $answer,
      $questionTypeId,
      $teacherId,
      $slug,
      $answeredAt,
      $questionId
    );
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function deleteQuestion($slug) {
    $sql = "DELETE FROM question
            WHERE  slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $rowsDeleted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsDeleted;
  }

  public function getAllQuestionTypes() {
    $sql = "SELECT *
            FROM   question_type";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function addToQuestionAlert($questionId, $personId) {
    $sql = "INSERT INTO question_alert (question_id, person_id, created_at, updated_at)
            VALUES (?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $questionId, $personId);
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function removeFromQuestionAlert($questionId, $personId) {
    $sql = "DELETE FROM question_alert
            WHERE  question_id = ?
            AND    person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $questionId, $personId);
    $stmt->execute();
    $rowsDeleted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsDeleted;
  }

  public function shouldUserReceiveAlert($questionId, $personId) {
    $sql = "SELECT question_id
	        FROM   question_alert
            WHERE  question_id = ?
            AND    person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $questionId, $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getFollowers($questionId) {
    $sql = "SELECT person_id
            FROM   question_alert
            WHERE  question_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $questionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getQuestionFavourited($questionId, $personId) {
    $sql = "SELECT *
	        FROM   question_favourited
            WHERE  person_id = ?
            AND    question_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $personId, $questionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function addToQuestionFavourited($questionId, $personId) {
    $sql = "INSERT INTO question_favourited (question_id, person_id, favourited_at)
            VALUES (?, ?, now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $questionId, $personId);
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function removeFromQuestionFavourited($questionId, $personId) {
    $sql = "DELETE FROM question_favourited
            WHERE  question_id = ?
            AND    person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $questionId, $personId);
    $stmt->execute();
    $rowsDeleted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsDeleted;
  }

  public function getFavouriteQuestionsByPersonId($personId) {
    $sql = "SELECT q.question_id, q.person_id, q.question_title,
                   q.teacher_id, q.created_at, q.answered_at, q.updated_at,
                   q.published, q.question_type_id,
                   qt.description question_category,
                   qt.category_slug,
                   q.slug
            FROM   question_favourited qf
            JOIN   question q on q.question_id = qf.question_id
            JOIN   question_type qt on q.question_type_id = qt.question_type_id
            WHERE  qf.person_id = ?
            ORDER BY q.created_at DESC";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }
}
?>
