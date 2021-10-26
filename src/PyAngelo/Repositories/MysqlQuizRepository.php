<?php
namespace PyAngelo\Repositories;

class MysqlQuizRepository implements QuizRepository {
  protected $dbh;

  public function __construct(\Mysqli $dbh) {
    $this->dbh = $dbh;
  }

  public function getSkillBySlug($slug) {
    $sql = "SELECT *
            FROM   skill
            WHERE  slug = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getTutorialSkillsMastery($tutorialId, $personId) {
    $sql = "SELECT s.*,
                   ifnull(ml.mastery_level_desc, 'Not started') as mastery_level_desc
	          FROM   skill s
            JOIN   tutorial_skill ts ON s.skill_id = ts.skill_id
            LEFT JOIN skill_mastery sm ON sm.skill_id = s.skill_id AND sm.person_id = ?
            LEFT JOIN mastery_level ml on ml.mastery_level_id = sm.mastery_level_id
            WHERE  ts.tutorial_id = ?
            ORDER BY s.skill_id";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $personId, $tutorialId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getSkillMastery($skillId, $personId) {
    $sql = "SELECT s.*,
                   ifnull(ml.mastery_level_desc, 'Not started') as mastery_level_desc
	          FROM   skill s
            LEFT JOIN skill_mastery sm ON sm.skill_id = s.skill_id AND sm.person_id = ?
            LEFT JOIN mastery_level ml on ml.mastery_level_id = sm.mastery_level_id
            WHERE  s.skill_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $personId, $skillId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getAllSkillQuestions($skillId) {
    $sql = "SELECT sq.skill_question_id,
                   sq.skill_id
            FROM   skill_question sq
            WHERE  sq.skill_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $skillId);
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
            JOIN   tutorial_skill ts on ts.skill_id = s.skill_id
            WHERE  ts.tutorial_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $tutorialId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function createQuiz($quizTypeId, $keyId, $personId) {
    if ($quizTypeId == 1) {
      $tutorialCategoryId = NULL;
      $tutorialId = NULL;
      $skillId = $keyId;
    }
    else if ($quizTypeId == 2) {
      $tutorialCategoryId = NULL;
      $tutorialId = $keyId;
      $skillId = NULL;
    }
    else if ($quizTypeId == 3) {
      $tutorialCategoryId = $keyId;
      $tutorialId = NULL;
      $skillId = NULL;
    }
    else {
      $tutorialCategoryId = NULL;
      $tutorialId = NULL;
      $skillId = NULL;
    }
    $sql = "INSERT INTO quiz (
              quiz_id,
              quiz_type_id,
              tutorial_category_id,
              tutorial_id,
              skill_id,
              person_id,
              created_at,
              started_at,
              completed_at
            )
            VALUES (NULL, ?, ?, ?, ?, ?, now(), NULL, NULL)";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('iiiii', $quizTypeId, $tutorialCategoryId, $tutorialId, $skillId, $personId);
    $stmt->execute();
    $quizId = $this->dbh->insert_id;
    $stmt->close();
    return $quizId;
  }

  public function addQuizQuestion($tutorialId, $skillQuestionId) {
    $sql = "INSERT INTO quiz_question (
              quiz_id,
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

  public function getIncompleteSkillQuiz($skillId, $personId) {
    $sql = "SELECT q.*
            FROM   quiz q
            WHERE  q.skill_id = ?
            AND    q.person_id = ?
            AND    q.completed_at is NULL
            AND    q.quiz_type_id = 1";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $skillId, $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getIncompleteSkillQuizInfo($skillId, $personId) {
    $sql = "SELECT q.quiz_id,
                   count(*) question_count
            FROM   quiz q
            JOIN   quiz_question qq ON qq.quiz_id = q.quiz_id
            WHERE  q.skill_id = ?
            AND    q.person_id = ?
            AND    q.completed_at is NULL
            AND    q.quiz_type_id = 1
            GROUP BY q.quiz_id";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $skillId, $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getIncompleteTutorialQuiz($tutorialId, $personId) {
    $sql = "SELECT q.*
            FROM   quiz q
            WHERE  q.tutorial_id = ?
            AND    q.person_id = ?
            AND    q.completed_at is NULL
            AND    q.quiz_type_id = 2";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $tutorialId, $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getIncompleteTutorialQuizInfo($tutorialId, $personId) {
    $sql = "SELECT q.quiz_id,
                   count(*) question_count
            FROM   quiz q
            JOIN   quiz_question qq ON qq.quiz_id = q.quiz_id
            WHERE  q.tutorial_id = ?
            AND    q.person_id = ?
            AND    q.completed_at is NULL
            AND    q.quiz_type_id = 2
            GROUP BY q.quiz_id";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $tutorialId, $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getQuizOptions($quizId) {
    $sql = "SELECT q.quiz_id,
                   q.person_id,
                   sq.skill_question_id,
                   sq.skill_question_type_id,
                   sq.question,
                   sqo.skill_question_option_id,
                   sqo.option_text,
                   sqo.option_order,
                   sqo.correct
            FROM   quiz q
            JOIN   quiz_question qq on qq.quiz_id = q.quiz_id
            JOIN   skill_question sq on sq.skill_question_id = qq.skill_question_id
            JOIN   skill_question_option sqo on sqo.skill_question_id = sq.skill_question_id
            JOIN   skill s on s.skill_id = sq.skill_id
            WHERE  q.quiz_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function updateQuiz(
    $quizId,
    $quizStartTime,
    $quizEndTime
  ) {
    $sql = "UPDATE quiz
            SET    started_at = ?,
                   completed_at = ?
            WHERE  quiz_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'ssi',
      $quizStartTime,
      $quizEndTime,
      $quizId
    );
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function updateQuizQuestion(
    $quizId,
    $skillQuestionId,
    $skillQuestionOptionId,
    $correctUnaided,
    $questionStartTime,
    $questionEndTime
  ) {
    $sql = "UPDATE quiz_question
            SET    skill_question_option_id = ?,
                   correct_unaided = ?,
                   started_at = ?,
                   answered_at = ?
            WHERE  quiz_id = ?
            AND    skill_question_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'iissii',
      $skillQuestionOptionId,
      $correctUnaided,
      $questionStartTime,
      $questionEndTime,
      $quizId,
      $skillQuestionId
    );
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function getQuizResultsAndSkillMastery($quizId) {
    $sql = "SELECT s.skill_id,
                   s.skill_name,
                   q.quiz_type_id,
                   IFNULL(sm.mastery_level_id, 0) mastery_level_id,
                   IFNULL(ml.mastery_level_desc, 'Not started') mastery_level_desc,
                   sum(qq.correct_unaided) correct,
                   count(*) total
            FROM   quiz q
            JOIN   quiz_question qq on q.quiz_id = qq.quiz_id
            JOIN   skill_question sq on sq.skill_question_id = qq.skill_question_id
            JOIN   skill s on s.skill_id = sq.skill_id
            LEFT JOIN skill_mastery sm on sm.skill_id = s.skill_id and sm.person_id = q.person_id
            LEFT JOIN mastery_level ml on ml.mastery_level_id = sm.mastery_level_id
            WHERE  q.quiz_id = ?
            GROUP BY s.skill_id, s.skill_name, q.quiz_type_id";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $quizId);
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

  public function insertOrUpdateSkillMastery($skillId, $personId, $masteryLevelId) {
    $sql = "INSERT INTO skill_mastery (
              skill_id,
              person_id,
              mastery_level_id,
              created_at,
              updated_at
            )
            VALUES (?, ?, ?, now(), now())
            ON DUPLICATE KEY UPDATE mastery_level_id = ?, updated_at = now()";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'iiii',
      $skillId,
      $personId,
      $masteryLevelId,
      $masteryLevelId
    );
    $stmt->execute();
    $skillId = $this->dbh->insert_id;
    $stmt->close();
    return $skillId;
  }
}
