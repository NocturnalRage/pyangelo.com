<?php
namespace PyAngelo\Repositories;

class MysqlSketchRepository implements SketchRepository {
  protected $dbh;

  public function __construct(\Mysqli $dbh) {
    $this->dbh = $dbh;
  }

  public function getSketches($personId) {
    $sql = "SELECT *
            FROM   sketch
            WHERE  person_id = ?
            ORDER BY updated_at DESC";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getAllSketches() {
    $sql = "SELECT *
            FROM   sketch
            ORDER BY updated_at DESC";
    $result = $this->dbh->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getSketchById($sketchId) {
    $sql = "SELECT *
	          FROM   sketch
            WHERE  sketch_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $sketchId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getSketchByPersonAndTutorial($personId, $tutorialId) {
    $sql = "SELECT *
	          FROM   sketch
            WHERE  person_id = ?
            AND    tutorial_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $personId, $tutorialId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getSketchByPersonAndLesson($personId, $lessonId) {
    $sql = "SELECT *
	          FROM   sketch
            WHERE  person_id = ?
            AND    lesson_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $personId, $lessonId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getSketchFiles($sketchId) {
    $sql = "SELECT s.person_id,
                   sf.file_id,
                   sf.sketch_id,
                   sf.filename,
                   sf.created_at,
                   sf.updated_at
            FROM   sketch s
            JOIN   sketch_files sf on s.sketch_id = sf.sketch_id
            WHERE  s.sketch_id = ?
            ORDER by file_id";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $sketchId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function createNewSketch($personId, $title, $lessonId = NULL, $tutorialId = NULL) {
    $sql = "INSERT INTO sketch (
              sketch_id,
              person_id,
              lesson_id,
              tutorial_id,
              title,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, ?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'iiis',
      $personId,
      $lessonId,
      $tutorialId,
      $title
    );
    $stmt->execute();
    $sketchId = $this->dbh->insert_id;
    $stmt->close();

    $sql = "INSERT INTO sketch_files (
              file_id,
              sketch_id,
              filename,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, 'main.py', now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $sketchId);
    $stmt->execute();
    $fileId = $this->dbh->insert_id;
    $stmt->close();

    return $sketchId;
  }

  public function addSketchFile($sketchId, $filename) {
    $sql = "INSERT INTO sketch_files (
              file_id,
              sketch_id,
              filename,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('is', $sketchId, $filename);
    $stmt->execute();
    $fileId = $this->dbh->insert_id;
    $stmt->close();

    return $fileId;
  }

  public function forkSketch($sketchId, $personId, $title, $lessonId = NULL, $tutorialId = NULL) {
    $sql = "INSERT INTO sketch (
              sketch_id,
              person_id,
              lesson_id,
              tutorial_id,
              title,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, ?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'iiis',
      $personId,
      $lessonId,
      $tutorialId,
      $title
    );
    $stmt->execute();
    $newSketchId = $this->dbh->insert_id;
    $stmt->close();

    $sql = "INSERT INTO sketch_files (
              file_id,
              sketch_id,
              filename,
              created_at,
              updated_at
            )
            SELECT NULL, ?, filename, created_at, updated_at
            FROM   sketch_files
            WHERE  sketch_id = ?
            ORDER BY file_id";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $newSketchId, $sketchId);
    $stmt->execute();
    $fileId = $this->dbh->insert_id;
    $stmt->close();

    return $newSketchId;
  }

  public function renameSketch($sketchId, $title) {
    $sql = "UPDATE sketch
            SET    title = ?
            WHERE  sketch_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('si', $title, $sketchId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function updateSketchUpdatedAt($sketchId) {
    $sql = "UPDATE sketch
            SET    updated_at = now()
            WHERE  sketch_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $sketchId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }
}
?>
