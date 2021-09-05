<?php
namespace PyAngelo\Repositories;

class MysqlClassRepository implements ClassRepository {
  protected $dbh;

  public function __construct(\Mysqli $dbh) {
    $this->dbh = $dbh;
  }

  public function getTeacherClasses($personId) {
    $sql = "SELECT *
            FROM   class
            WHERE  person_id = ?
            ORDER BY updated_at DESC";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getStudentClasses($personId) {
    $sql = "SELECT c.class_id,
                   c.class_name,
                   cs.joined_at
            FROM   class_student cs
            JOIN   class c on c.class_id = cs.class_id
            WHERE  cs.person_id = ?
            ORDER BY joined_at DESC";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }
  public function createNewClass($personId, $className, $classCode) {
    $sql = "INSERT INTO class (
              class_id,
              person_id,
              class_name,
              class_code,
              archived,
              archived_at,
              created_at,
              updated_at
            )
            VALUES (NULL, ?, ?, ?, 0, NULL, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('iss', $personId, $className, $classCode);
    $stmt->execute();
    $classId = $this->dbh->insert_id;
    $stmt->close();
    return $classId;
  }

  public function getClassById($classId) {
    $sql = "SELECT *
	          FROM   class
            WHERE  class_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $classId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }
  public function getClassByCode($classCode) {
    $sql = "SELECT *
	          FROM   class
            WHERE  class_code = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('s', $classCode);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }
  public function updateClass($classId, $className) {
    $sql = "UPDATE class
            SET    class_name = ?
            WHERE  class_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('si', $className, $classId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }
  public function joinClass($classId, $personId) {
    $sql = "INSERT INTO class_student (
              class_id,
              person_id,
              joined_at,
              updated_at
            )
            VALUES (?, ?, now(), now())";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $classId, $personId);
    $stmt->execute();
    $rowsInserted = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsInserted;
  }
  public function getClassStudents($classId) {
    $sql = "SELECT p.person_id,
                   p.given_name,
                   p.family_name,
                   p.email,
                   cs.joined_at
            FROM   person p
            JOIN   class_student cs on cs.person_id = p.person_id
            WHERE  cs.class_id = ?
            ORDER BY p.given_name, p.family_name";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $classId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }
  public function getStudentFromClass($classId, $personId) {
    $sql = "SELECT p.person_id,
                   p.given_name,
                   p.family_name,
                   p.email,
                   cs.joined_at
	          FROM   class_student cs
            JOIN   person p on p.person_id = cs.person_id
            WHERE  cs.class_id = ?
            AND    cs.person_id = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $classId, $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc();
  }
  public function getStudentSketches($classId, $personId) {
    $sql = "SELECT s.*,
                   cs.joined_at
            FROM   class_student cs
            JOIN   person p on p.person_id = cs.person_id
            JOIN   sketch s on s.person_id = p.person_id
            WHERE  cs.class_id = ?
            AND    cs.person_id = ?
            ORDER BY s.updated_at DESC";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('ii', $classId, $personId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC);
  }
  public function archiveClass($classId) {
    $sql = "UPDATE class
            SET    archived = TRUE,
                   archived_at = now()
            WHERE  class_id  = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $classId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }
  public function restoreClass($classId) {
    $sql = "UPDATE class
            SET    archived = FALSE,
                   archived_at = NULL
            WHERE  class_id  = ?";
    $stmt = $this->dbh->prepare($sql);
    $stmt->bind_param('i', $classId);
    $stmt->execute();
    $rowsUpdated = $this->dbh->affected_rows;
    $stmt->close();
    return $rowsUpdated;
  }
}
?>
