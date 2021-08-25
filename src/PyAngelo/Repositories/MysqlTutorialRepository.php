<?php
namespace PyAngelo\Repositories;

class MysqlTutorialRepository implements TutorialRepository {
  protected $dbh;

  public function __construct(\Mysqli $dbh) {
    $this->dbh = $dbh;
  }

  public function getAllTutorials() {
    $sql = "SELECT t.*,
                   tl.description as level,
                   tc.category, tc.category_slug, tc.display_order
            FROM   tutorial t
            JOIN   tutorial_level tl on t.tutorial_level_id = tl.tutorial_level_id
            JOIN   tutorial_category tc on t.tutorial_category_id = tc.tutorial_category_id
            ORDER BY tc.display_order, t.display_order";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getTutorialsByCategory($slug) {
    $sql = "SELECT t.*, tl.description as level,
                   tc.tutorial_category_id, tc.category, tc.category_slug
            FROM   tutorial t
            JOIN   tutorial_level tl on t.tutorial_level_id = tl.tutorial_level_id
            JOIN   tutorial_category tc on t.tutorial_category_id = tc.tutorial_category_id
            WHERE  tc.category_slug = ?
            ORDER BY t.display_order";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getTutorialBySlug($slug) {
    $sql = "SELECT *
            FROM   tutorial
            WHERE  slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getTutorialBySlugWithStats($slug, $personId) {
    $sql = "SELECT t.tutorial_id, t.title, t.description,
                   t.thumbnail, t.pdf, t.slug,
                   tl.description as level,
                   tc.category, tc.category_slug,
                   SUM(l.seconds) as seconds,
                   COUNT(l.lesson_id) as lesson_count,
                   ROUND(
                     SUM(
                       CASE WHEN lc.lesson_id IS NULL
                            THEN 0 ELSE 1
                       END
                     ) / SUM(1) * 100
                   ) percent_complete
	        FROM   tutorial t
            JOIN   tutorial_level tl on tl.tutorial_level_id = t.tutorial_level_id
            JOIN   tutorial_category tc on tc.tutorial_category_id = t.tutorial_category_id
            LEFT JOIN lesson l on l.tutorial_id = t.tutorial_id
            LEFT JOIN lesson_completed lc on lc.lesson_id = l.lesson_id and lc.person_id = ?
            WHERE  t.slug = ?
            GROUP BY t.tutorial_id";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('is', $personId, $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getTutorialPercentComplete($personId, $lessonId) {
    $sql = "SELECT ROUND(
                     SUM(
                       CASE WHEN lc.lesson_id IS NULL
                            THEN 0 ELSE 1
                       END
                     ) / SUM(1) * 100
                   ) percent_complete
	        FROM   tutorial t
            LEFT JOIN lesson l on l.tutorial_id = t.tutorial_id
            LEFT JOIN lesson_completed lc on lc.lesson_id = l.lesson_id and lc.person_id = ?
            WHERE  t.tutorial_id in (select tutorial_id from lesson where lesson_id = ?)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $personId, $lessonId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $row = $result->fetch_assoc();
    return $row['percent_complete'];
  }

  public function getTutorialByTitle($title) {
    $sql = "SELECT *
	        FROM   tutorial
            WHERE  title = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $title);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function insertTutorial(
    $title,
    $description,
    $slug,
    $tutorialCategoryId,
    $tutorialLevelId,
    $singleSketch,
    $tutorialSketchId,
    $displayOrder,
    $thumbnail
  ) {
    $sql = "INSERT INTO tutorial (
              tutorial_id,
              title,
              description,
              slug,
              thumbnail,
              tutorial_category_id,
              tutorial_level_id,
              single_sketch,
              tutorial_sketch_id,
              display_order,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'ssssiiiii',
      $title,
      $description,
      $slug,
      $thumbnail,
      $tutorialCategoryId,
      $tutorialLevelId,
      $singleSketch,
      $tutorialSketchId,
      $displayOrder
    );
    $stmt->execute();
    $tutorialId = $this->dbh->insert_id;
    $stmt->close();
    return $tutorialId;
  }

  public function updateTutorialBySlug(
    $slug,
    $title,
    $description,
    $tutorialCategoryId,
    $tutorialLevelId,
    $singleSketch,
    $tutorialSketchId,
    $displayOrder
  ) {
    $sql = "UPDATE tutorial
            SET    title = ?,
                   description = ?,
                   tutorial_category_id = ?,
                   tutorial_level_id = ?,
                   single_sketch = ?,
                   tutorial_sketch_id = ?,
                   display_order = ?,
                   updated_at = now()
            WHERE  slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'ssiiiiis',
      $title,
      $description,
      $tutorialCategoryId,
      $tutorialLevelId,
      $singleSketch,
      $tutorialSketchId,
      $displayOrder,
      $slug
    );
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function updateTutorialOrder($slug, $position) {
    $sql = "UPDATE tutorial
            SET    display_order = ?,
                   updated_at = now()
            WHERE  slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'is',
      $position,
      $slug
    );
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function updateLessonOrder($tutorialId, $lessonSlug, $position) {
    $sql = "UPDATE lesson
            SET    display_order = ?,
                   updated_at = now()
            WHERE  tutorial_id = ?
            AND    lesson_slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'iis',
      $position,
      $tutorialId,
      $lessonSlug
    );
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function updateTutorialThumbnailBySlug($slug, $thumbnail) {
    $sql = "UPDATE tutorial
            SET    thumbnail = ?,
                   updated_at = now()
            WHERE  slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ss', $thumbnail, $slug);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function updateTutorialPdfBySlug($slug, $pdf) {
    $sql = "UPDATE tutorial
            SET    pdf = ?,
                   updated_at = now()
            WHERE  slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ss', $pdf, $slug);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function updateLessonPosterByTutorialIdAndSlug(
    $tutorialId,
    $lessonSlug,
    $poster
  ) {
    $sql = "UPDATE lesson
            SET    poster = ?,
                   updated_at = now()
            WHERE  tutorial_id = ?
            AND    lesson_slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('sis', $poster, $tutorialId, $lessonSlug);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function insertTutorialCategory($id, $category, $slug, $displayOrder) {
    $sql = "INSERT INTO tutorial_category (tutorial_category_id, category, category_slug, display_order)
            VALUES (?, ?, ?, ?)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('issi', $id, $category, $slug, $displayOrder);
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function insertTutorialLevel($id, $description) {
    $sql = "INSERT INTO tutorial_level (tutorial_level_id, description)
            VALUES (?, ?)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('is', $id, $description);
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function getAllTutorialLevels() {
    $sql = "SELECT *
            FROM   tutorial_level";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getAllTutorialCategories() {
    $sql = "SELECT *
            FROM   tutorial_category
            ORDER BY display_order";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getTutorialCategoryById($categoryId) {
    $sql = "SELECT *
	        FROM   tutorial_category
            WHERE  tutorial_category_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getTutorialLevelById($levelId) {
    $sql = "SELECT *
	        FROM   tutorial_level
            WHERE  tutorial_level_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $levelId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function insertLessonSecurityLevel($id, $description) {
    $sql = "INSERT INTO lesson_security_level (lesson_security_level_id, description)
            VALUES (?, ?)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('is', $id, $description);
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function getLessonSecurityLevelById($levelId) {
    $sql = "SELECT *
	        FROM   lesson_security_level
            WHERE  lesson_security_level_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $levelId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getAllLessonSecurityLevels() {
    $sql = "SELECT *
            FROM   lesson_security_level";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function insertLesson($lessonInfo) {
    $sql = "INSERT INTO lesson (
              lesson_id,
              tutorial_id,
              lesson_title,
              lesson_description,
              video_name,
              youtube_url,
              seconds,
              lesson_slug,
              lesson_security_level_id,
              lesson_sketch_id,
              display_order,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'issssisiii',
      $lessonInfo['tutorial_id'],
      $lessonInfo['lesson_title'],
      $lessonInfo['lesson_description'],
      $lessonInfo['video_name'],
      $lessonInfo['youtube_url'],
      $lessonInfo['seconds'],
      $lessonInfo['lesson_slug'],
      $lessonInfo['lesson_security_level_id'],
      $lessonInfo['lesson_sketch_id'],
      $lessonInfo['display_order']
    );
    $stmt->execute();
    $lessonId = $this->dbh->insert_id;
    $stmt->close();
    return $lessonId;
  }

  public function getLessonById($lessonId) {
    $sql = "SELECT l.*,
                   t.slug as tutorial_slug
	        FROM   lesson l
            JOIN   tutorial t ON l.tutorial_id = t.tutorial_id
            WHERE  lesson_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $lessonId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getLessonByTitleAndTutorialId($title, $tutorialId) {
    $sql = "SELECT *
            FROM   lesson
            WHERE  tutorial_id = ?
            AND    lesson_title = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('is', $tutorialId, $title);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function insertLessonCompleted($personId, $lessonId) {
    $sql = "INSERT INTO lesson_completed (person_id, lesson_id, completed_at)
            VALUES (?, ?, now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $personId, $lessonId);
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function insertLessonFavourited($personId, $lessonId) {
    $sql = "INSERT INTO lesson_favourited (person_id, lesson_id, favourited_at)
            VALUES (?, ?, now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $personId, $lessonId);
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function deleteLessonCompleted($personId, $lessonId) {
    $sql = "DELETE FROM lesson_completed
            WHERE  person_id = ?
            AND    lesson_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $personId, $lessonId);
    $stmt->execute();
    $rowsDeleted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsDeleted;
  }

  public function deleteLessonFavourited($personId, $lessonId) {
    $sql = "DELETE FROM lesson_favourited
            WHERE  person_id = ?
            AND    lesson_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $personId, $lessonId);
    $stmt->execute();
    $rowsDeleted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsDeleted;
  }

  public function getLessonCompleted($personId, $lessonId) {
    $sql = "SELECT *
	        FROM   lesson_completed
            WHERE  person_id = ?
            AND    lesson_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $personId, $lessonId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getLessonFavourited($personId, $lessonId) {
    $sql = "SELECT *
	        FROM   lesson_favourited
            WHERE  person_id = ?
            AND    lesson_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $personId, $lessonId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getAllFavourites($personId) {
    $sql = "SELECT l.*,
                   t.slug as tutorial_slug,
                   t.title as tutorial_title,
                   CASE WHEN lc.completed_at IS NULL THEN 0 ELSE 1 END AS completed,
                CASE
                  WHEN seconds < 60
                  THEN DATE_FORMAT(SEC_TO_TIME(l.seconds), '0:%s')
                  WHEN l.seconds < 3600
                  THEN TRIM(LEADING 0 FROM DATE_FORMAT(SEC_TO_TIME(l.seconds), '%i:%s'))
                  WHEN l.seconds >= 3600
                  THEN TRIM(LEADING 0 FROM DATE_FORMAT(SEC_TO_TIME(l.seconds), '%l:%i:%s'))
                END as display_duration
            FROM   lesson_favourited lf
            JOIN   lesson l ON lf.lesson_id = l.lesson_id
            JOIN   tutorial t ON l.tutorial_id = t.tutorial_id
            LEFT JOIN lesson_completed lc ON lc.lesson_id = l.lesson_id AND lc.person_id = ?
            WHERE  lf.person_id = ?
            ORDER BY lf.favourited_at desc";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $personId, $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getTutorialLessons($tutorialId, $personId) {
    $sql = "SELECT l.*,
                   t.slug as tutorial_slug,
                   CASE WHEN lc.completed_at IS NULL THEN 0 ELSE 1 END AS completed,
                CASE
                  WHEN seconds < 60
                  THEN DATE_FORMAT(SEC_TO_TIME(l.seconds), '0:%s')
                  WHEN l.seconds < 3600
                  THEN TRIM(LEADING 0 FROM DATE_FORMAT(SEC_TO_TIME(l.seconds), '%i:%s'))
                  WHEN l.seconds >= 3600
                  THEN TRIM(LEADING 0 FROM DATE_FORMAT(SEC_TO_TIME(l.seconds), '%l:%i:%s'))
                END as display_duration
	        FROM   lesson l
            JOIN   tutorial t ON l.tutorial_id = t.tutorial_id
            LEFT JOIN lesson_completed lc ON lc.lesson_id = l.lesson_id AND lc.person_id = ?
            WHERE  t.tutorial_id = ?
            ORDER BY l.display_order";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $personId, $tutorialId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getTutorialSkills($tutorialId, $personId) {
    $sql = "SELECT s.*,
                   ifnull(ml.mastery_level_desc, 'Not started') as mastery_level_desc
	          FROM   skill s
            JOIN   tutorial t ON s.tutorial_id = t.tutorial_id
            LEFT JOIN skill_mastery sm ON sm.skill_id = s.skill_id AND sm.person_id = ?
            LEFT JOIN mastery_level ml on ml.mastery_level_id = sm.mastery_level_id
            WHERE  t.tutorial_id = ?
            ORDER BY s.skill_id";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $personId, $tutorialId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getAllTutorialQuestions($tutorialId) {
    $sql = "SELECT sq.skill_question_id,
                   sq.skill_id
            FROM   skill_question sq
            JOIN   skill s on s.skill_id = sq.skill_id
            JOIN   tutorial t on t.tutorial_id = s.tutorial_id
            WHERE  t.tutorial_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $tutorialId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }


  public function createTutorialQuiz($tutorialId, $personId) {
    $sql = "INSERT INTO tutorial_quiz (
              tutorial_quiz_id,
              tutorial_id,
              person_id,
              created_at,
              started_at,
              completed_at
            )
            VALUES (NULL, ?, ?, now(), NULL, NULL)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $tutorialId, $personId);
    $stmt->execute();
    $tutorialQuizId = $this->dbh->insert_id;
    $stmt->close();
    return $tutorialQuizId;
  }

  public function addTutorialQuizQuestion($tutorialId, $skillQuestionId) {
    $sql = "INSERT INTO tutorial_quiz_question (
              tutorial_quiz_id,
              skill_question_id,
              skill_question_option_id,
              created_at,
              started_at,
              answered_at
            )
            VALUES (?, ?, NULL, now(), NULL, NULL)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $tutorialId, $skillQuestionId);
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function getUncompletedTutorialQuiz($tutorialId, $personId) {
    $sql = "SELECT tq.*
            FROM   tutorial_quiz tq
            WHERE  tq.tutorial_id = ?
            AND    tq.person_id = ?
            AND    tq.completed_at is NULL";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $tutorialId, $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getUncompletedTutorialQuizInfo($tutorialId, $personId) {
    $sql = "SELECT tq.tutorial_quiz_id,
                   count(*) question_count
            FROM   tutorial_quiz tq
            JOIN   tutorial_quiz_question tqq ON tqq.tutorial_quiz_id = tq.tutorial_quiz_id
            WHERE  tq.tutorial_id = ?
            AND    tq.person_id = ?
            AND    completed_at is NULL
            GROUP BY tq.tutorial_quiz_id";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $tutorialId, $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getTutorialQuizOptions($tutorialQuizId) {
    $sql = "SELECT tq.tutorial_quiz_id,
                   t.slug,
                   tq.person_id,
                   sq.skill_question_id,
                   sq.skill_question_type_id,
                   sq.question,
                   sqo.skill_question_option_id,
                   sqo.option_text,
                   sqo.option_order,
                   sqo.correct
            FROM   tutorial_quiz tq
            JOIN   tutorial t on t.tutorial_id = tq.tutorial_id
            JOIN   tutorial_quiz_question tqq on tqq.tutorial_quiz_id = tq.tutorial_quiz_id
            JOIN   skill_question sq on sq.skill_question_id = tqq.skill_question_id
            JOIN   skill_question_option sqo on sqo.skill_question_id = sq.skill_question_id
            WHERE  tq.tutorial_quiz_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $tutorialQuizId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function updateTutorialQuiz(
    $tutorialQuizId,
    $quizStartTime,
    $quizEndTime
  ) {
    $sql = "UPDATE tutorial_quiz
            SET    started_at = ?,
                   completed_at = ?
            WHERE  tutorial_quiz_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'ssi',
      $quizStartTime,
      $quizEndTime,
      $tutorialQuizId
    );
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function updateTutorialQuizQuestion(
    $tutorialQuizId,
    $skillQuestionId,
    $skillQuestionOptionId,
    $correctUnaided,
    $questionStartTime,
    $questionEndTime
  ) {
    $sql = "UPDATE tutorial_quiz_question
            SET    skill_question_option_id = ?,
                   correct_unaided = ?,
                   started_at = ?,
                   answered_at = ?
            WHERE  tutorial_quiz_id = ?
            AND    skill_question_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'iissii',
      $skillQuestionOptionId,
      $correctUnaided,
      $questionStartTime,
      $questionEndTime,
      $tutorialQuizId,
      $skillQuestionId
    );
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function getQuizResultsAndSkillMastery($tutorialQuizId) {
    $sql = "SELECT s.skill_id,
                   s.skill_name,
                   IFNULL(sm.mastery_level_id, 0) mastery_level_id,
                   IFNULL(ml.mastery_level_desc, 'Not started') mastery_level_desc,
                   sum(correct_unaided) correct,
                   count(*) total
            FROM   tutorial_quiz tq
            JOIN   tutorial_quiz_question tqq on tq.tutorial_quiz_id = tqq.tutorial_quiz_id
            JOIN   skill_question sq on sq.skill_question_id = tqq.skill_question_id
            JOIN   skill s on s.skill_id = sq.skill_id
            LEFT JOIN skill_mastery sm on sm.skill_id = s.skill_id and sm.person_id = tq.person_id
            LEFT JOIN mastery_level ml on ml.mastery_level_id = sm.mastery_level_id
            WHERE  tq.tutorial_quiz_id = ?
            GROUP BY s.skill_id, s.skill_name";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $tutorialQuizId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getSkillQuestionHints($skillQuestionId) {
    $sql = "SELECT h.hint_id,
                   h.skill_question_id,
                   h.hint,
                   h.hint_order
            FROM   skill_question_hint h
            WHERE  h.skill_question_id = ?
            ORDER BY h.hint_order";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $skillQuestionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getSkillMastery($skillId, $personId) {
    $sql = "SELECT sm.skill_id,
                   sm.person_id,
                   sm.mastery_level_id
            FROM   skill_mastery sm
            WHERE  sm.skill_id = ?
            AND    sm.person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $skillId, $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function updateSkillMastery($skillId, $personId, $masteryLevelId) {
    $sql = "UPDATE skill_mastery
            SET    mastery_level_id = ?,
                   updated_at = now()
            WHERE  skill_id = ?
            AND    person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'iii',
      $masteryLevelId,
      $skillId,
      $personId
    );
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function insertSkillMastery($skillId, $personId, $masteryLevelId) {
    $sql = "INSERT INTO skill_mastery (
              skill_id,
              person_id,
              mastery_level_id,
              created_at,
              updated_at
            )
            VALUES (?, ?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'iii',
      $skillId,
      $personId,
      $masteryLevelId
    );
    $stmt->execute();
    $commentId = $this->dbh->insert_id;
    $stmt->close();
    return $commentId;
  }

  public function getLessonBySlugAndTutorialId($lessonSlug, $tutorialId) {
    $sql = "SELECT *
            FROM   lesson
            WHERE  tutorial_id = ?
            AND    lesson_slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('is', $tutorialId, $lessonSlug);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getLessonBySlugs($tutorialSlug, $lessonSlug) {
    $sql = "SELECT l.*,
                   t.tutorial_id,
                   t.title as tutorial_title,
                   t.slug as tutorial_slug,
                   t.single_sketch,
                   t.thumbnail as tutorial_thumbnail
	        FROM   lesson l
            JOIN   tutorial t on l.tutorial_id = t.tutorial_id
            WHERE  t.slug = ?
            AND    l.lesson_slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ss', $tutorialSlug, $lessonSlug);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getLessonBySlugsWithStatus($tutorialSlug, $lessonSlug, $personId) {
    $sql = "SELECT l.*,
                   CASE WHEN lc.completed_at IS NULL THEN 0 ELSE 1 END AS completed,
                   CASE WHEN lf.favourited_at IS NULL THEN 0 ELSE 1 END AS favourited,
                   t.tutorial_id,
                   t.single_sketch,
                   t.tutorial_sketch_id,
                   t.title as tutorial_title,
                   t.slug as tutorial_slug,
                   t.thumbnail as tutorial_thumbnail,
                   t.pdf
            FROM   lesson l
            LEFT JOIN lesson_completed lc on lc.lesson_id = l.lesson_id and lc.person_id = ?
            LEFT JOIN lesson_favourited lf on lf.lesson_id = l.lesson_id and lf.person_id = ?
            JOIN   tutorial t on l.tutorial_id = t.tutorial_id
            WHERE  t.slug = ?
            AND    l.lesson_slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('iiss', $personId, $personId, $tutorialSlug, $lessonSlug);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getLessonCaptions($tutorialId, $lessonSlug) {
    $sql = "SELECT lc.caption_filename, cl.language, cl.srclang
            FROM   lesson l
            JOIN   lesson_caption lc on l.lesson_id = lc.lesson_id
            JOIN   caption_language cl on cl.caption_language_id = lc.caption_language_id
            WHERE  l.tutorial_id = ?
            AND    l.lesson_slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('is', $tutorialId, $lessonSlug);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getCaptionLanguages() {
    $sql = "SELECT *
            FROM   caption_language";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getCaptionLanguageById($captionLanguageId) {
    $sql = "SELECT *
	        FROM   caption_language
            WHERE  caption_language_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $captionLanguageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function insertOrUpdateCaption(
    $lessonId,
    $captionLanguageId,
    $captionFilename
  ) {
    $sql = "INSERT INTO lesson_caption (lesson_id, caption_language_id, caption_filename)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE caption_filename = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'iiss',
      $lessonId,
      $captionLanguageId,
      $captionFilename,
      $captionFilename
    );
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function updateLessonByTutorialIdAndSlug($formData) {
    $sql = "UPDATE lesson
            SET    lesson_title = ?,
                   lesson_description = ?,
                   video_name = ?,
                   youtube_url = ?,
                   seconds = ?,
                   lesson_security_level_id = ?,
                   lesson_sketch_id = ?,
                   display_order = ?,
                   updated_at = now()
            WHERE  tutorial_id = ?
            AND    lesson_slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'ssssiiiiis',
      $formData['lesson_title'],
      $formData['lesson_description'],
      $formData['video_name'],
      $formData['youtube_url'],
      $formData['seconds'],
      $formData['lesson_security_level_id'],
      $formData['lesson_sketch_id'],
      $formData['display_order'],
      $formData['tutorial_id'],
      $formData['lesson_slug']
    );
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function getNextLessonInTutorial($tutorialId, $displayOrder) {
    $sql = "SELECT l.*, t.slug as tutorial_slug
	        FROM   lesson l
            JOIN   tutorial t on l.tutorial_id = t.tutorial_id
            WHERE  l.tutorial_id = ?
            AND    l.display_order > ?
            ORDER BY l.display_order
            LIMIT 1";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $tutorialId, $displayOrder);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function insertLessonComment($commentData) {
    $sql = "INSERT INTO lesson_comment (
              comment_id,
              lesson_id,
              person_id,
              lesson_comment,
              published,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, ?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'iisi',
      $commentData['lesson_id'],
      $commentData['person_id'],
      $commentData['lesson_comment'],
      $commentData['published']
    );
    $stmt->execute();
    $commentId = $this->dbh->insert_id;
    $stmt->close();
    return $commentId;
  }

  public function getPublishedLessonComments($lessonId) {
    $sql = "SELECT concat(p.given_name, ' ', p.family_name) as display_name,
                   p.email,
                   p.admin,
                   lc.*
            FROM   lesson_comment lc
            JOIN   person p ON p.person_id = lc.person_id
            WHERE  lc.lesson_id = ?
            AND    lc.published = 1
            ORDER BY lc.created_at";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $lessonId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getLatestComments($offset, $limit) {
    $sql = "SELECT lc.comment_id, lc.lesson_id, lc.person_id,
                   lc.lesson_comment, lc.created_at,
                   l.lesson_title, l.lesson_slug, t.slug as tutorial_slug,
                   p.person_id, p.email, p.admin,
                   concat(p.given_name, ' ', p.family_name) as display_name
            FROM   lesson_comment lc
            JOIN   lesson l ON lc.lesson_id = l.lesson_id
            JOIN   tutorial t ON l.tutorial_id = t.tutorial_id
            JOIN   person p ON lc.person_id = p.person_id
            WHERE  lc.published = TRUE
            ORDER BY lc.created_at DESC
            LIMIT ?, ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $offset, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }


  public function unpublishCommentById($commentId) {
    $sql = "UPDATE lesson_comment
            SET    published = 0
            WHERE  comment_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $commentId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function deleteAllTutorials() {
    $sql = "DELETE FROM tutorial";
    if (!($result = $this->dbh->query($sql))) {
      return false;
    }
    return $this->dbh->affected_rows;
  }

  public function deleteAllTutorialCategories() {
    $sql = "DELETE FROM tutorial_category";
    if (!($result = $this->dbh->query($sql))) {
      return false;
    }
    return $this->dbh->affected_rows;
  }

  public function deleteAllTutorialLevels() {
    $sql = "DELETE FROM tutorial_level";
    if (!($result = $this->dbh->query($sql))) {
      return false;
    }
    return $this->dbh->affected_rows;
  }

  public function deleteAllLessons() {
    $sql = "DELETE FROM lesson";
    if (!($result = $this->dbh->query($sql))) {
      return false;
    }
    return $this->dbh->affected_rows;
  }

  public function deleteAllLessonCaptions() {
    $sql = "DELETE FROM lesson_caption";
    if (!($result = $this->dbh->query($sql))) {
      return false;
    }
    return $this->dbh->affected_rows;
  }

  public function deleteAllLessonSecurityLevels() {
    $sql = "DELETE FROM lesson_security_level";
    if (!($result = $this->dbh->query($sql))) {
      return false;
    }
    return $this->dbh->affected_rows;
  }

  public function addToLessonAlert($lessonId, $personId) {
    $sql = "INSERT INTO lesson_alert (lesson_id, person_id, created_at, updated_at)
            VALUES (?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $lessonId, $personId);
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }

  public function removeFromLessonAlert($lessonId, $personId) {
    $sql = "DELETE FROM lesson_alert
            WHERE  lesson_id = ?
            AND    person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $lessonId, $personId);
    $stmt->execute();
    $rowsDeleted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsDeleted;
  }

  public function shouldUserReceiveAlert($lessonId, $personId) {
    $sql = "SELECT lesson_id
            FROM   lesson_alert
            WHERE  lesson_id = ?
            AND    person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $lessonId, $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getFollowers($lessonId) {
    $sql = "SELECT person_id
            FROM   lesson_alert
            WHERE  lesson_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $lessonId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }
}
