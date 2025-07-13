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
            WHERE  sketch_id = ?
            AND    deleted = FALSE";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $sketchId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getDeletedSketchById($sketchId) {
    $sql = "SELECT *
	          FROM   sketch
            WHERE  sketch_id = ?
            AND    deleted = TRUE";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $sketchId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getSketchByPersonAndTutorial($personId, $tutorialId) {
    $sql = "SELECT *
	          FROM   sketch
            WHERE  person_id = ?
            AND    tutorial_id = ?
            AND    deleted = FALSE";
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
            AND    lesson_id = ?
            AND    deleted = FALSE";
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
    $stmt->bind_param('s', $sketchId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function createNewSketch($personId, $title, $collectionId, $lessonId = NULL, $tutorialId = NULL, $layout = 'cols') {
    $sketchId = bin2hex(random_bytes(16));
    $sql = "INSERT INTO sketch (
              sketch_id,
              person_id,
              collection_id,
              lesson_id,
              tutorial_id,
              title,
              layout,
              created_at,
              updated_at
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'siiiiss',
      $sketchId,
      $personId,
      $collectionId,
      $lessonId,
      $tutorialId,
      $title,
      $layout
    );
    $stmt->execute();
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
    $stmt->bind_param('s', $sketchId);
    $stmt->execute();
    $fileId = $this->dbh->insert_id;
    $stmt->close();

    return $sketchId;
  }

  public function deleteSketch($sketchId) {
    $sql = "UPDATE sketch
            SET    deleted = TRUE,
                   deleted_at = now(),
                   tutorial_id = NULL,
                   lesson_id = NULL
            WHERE  sketch_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $sketchId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function getSketchesToDelete() {
    $sql = "SELECT *
            FROM   sketch
            WHERE  deleted = TRUE
            AND    deleted_at < NOW() - INTERVAL 90 DAY";
    $stmt = $this->dbh->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function deleteSketchForever($sketchId) {
    $sql = "DELETE
            FROM   sketch
            WHERE  sketch_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $sketchId);
    $stmt->execute();
    $rowsDeleted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsDeleted;
  }

  public function restoreSketch($sketchId) {
    $sql = "UPDATE sketch
            SET    deleted = FALSE,
                   deleted_at = NULL
            WHERE   sketch_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $sketchId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
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
    $stmt->bind_param('ss', $sketchId, $filename);
    $stmt->execute();
    $fileId = $this->dbh->insert_id;
    $stmt->close();

    return $fileId;
  }
  
  public function deleteSketchFile($sketchId, $filename) {
    $sql = "DELETE
            FROM    sketch_files
            WHERE   sketch_id = ?
            AND     filename = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ss', $sketchId, $filename);
    $stmt->execute();
    $rowsDeleted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsDeleted;
  }

  public function deleteSketchFiles($sketchId) {
    $sql = "DELETE
            FROM    sketch_files
            WHERE   sketch_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $sketchId);
    $stmt->execute();
    $rowsDeleted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsDeleted;
  }

  public function forkSketch($sketchId, $personId, $title, $lessonId = NULL, $tutorialId = NULL, $layout = 'cols') {
    $newSketchId = bin2hex(random_bytes(16));
    $sql = "INSERT INTO sketch (
              sketch_id,
              person_id,
              lesson_id,
              tutorial_id,
              title,
              layout,
              created_at,
              updated_at
            )
            VALUES (?, ?, ?, ?, ?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'siiiss',
      $newSketchId,
      $personId,
      $lessonId,
      $tutorialId,
      $title,
      $layout
    );
    $stmt->execute();
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
    $stmt->bind_param('ss', $newSketchId, $sketchId);
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
    $stmt->bind_param('ss', $title, $sketchId);
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
    $stmt->bind_param('s', $sketchId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function updateSketchLayout($sketchId, $layout) {
    $sql = "UPDATE sketch
            SET    layout = ?,
                   updated_at = now()
            WHERE  sketch_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ss', $layout, $sketchId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function createNewCollection($personId, $title) {
    $sql = "INSERT INTO sketch_collection (
              collection_id,
              person_id,
              collection_name,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param(
      'is',
      $personId,
      $title
    );
    $stmt->execute();
    $collectionId = $this->dbh->insert_id;
    $stmt->close();
    return $collectionId;
  }

  public function getCollections($personId) {
    $sql = "SELECT *
            FROM   sketch_collection
            WHERE  person_id = ?
            ORDER BY collection_name";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function addSketchToCollection($sketchId, $collectionId) {
    $sql = "UPDATE sketch
            SET    collection_id = ?,
                   updated_at = now()
            WHERE  sketch_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('is', $collectionId, $sketchId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function removeSketchFromAllCollections($sketchId) {
    $sql = "UPDATE sketch
            SET    collection_id = NULL,
                   updated_at = now()
            WHERE  sketch_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $sketchId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }

  public function getCollectionById($collectionId) {
    $sql = "SELECT *
	          FROM   sketch_collection
            WHERE  collection_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $collectionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }

  public function getCollectionSketches($collectionId) {
    $sql = "SELECT *
            FROM   sketch
            WHERE  collection_id = ?
            AND    deleted = FALSE
            ORDER BY updated_at DESC";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $collectionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function renameCollection($collectionId, $title) {
    $sql = "UPDATE sketch_collection
            SET    collection_name = ?
            WHERE  collection_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('si', $title, $collectionId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }
}
?>
