<?php
namespace PyAngelo\Repositories;

interface classRepository {
  public function getTeacherClasses($personId);
  public function getStudentClasses($personId);
  public function createNewClass($personId, $className, $classCode);
  public function getClassById($classId);
  public function getClassByCode($classCode);
  public function updateClass($classId, $className);
  public function joinClass($classId, $personId);
  public function getClassStudents($classId);
  public function getStudentFromClass($classId, $personId);
  public function getStudentSketches($classId, $personId);
  public function archiveClass($classId);
  public function restoreClass($classId);
}
?>
