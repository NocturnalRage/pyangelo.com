<?php
namespace PyAngelo\Repositories;

interface SketchRepository {

  public function getSketches($personId);

  public function getAllSketches();
 
  public function getSketchById($sketchId);

  public function getSketchByPersonAndTutorial($personId, $lessonId);

  public function getSketchByPersonAndLesson($personId, $lessonId);

  public function getSketchFiles($sketchId);

  public function createNewSketch($personId, $title, $lessonId = NULL);

  public function addSketchFile($sketchId, $filename);
  
  public function deleteSketchFile($sketchId, $filename);

  public function forkSketch($sketchId, $personId, $title);

  public function renameSketch($sketchId, $title);

  public function updateSketchUpdatedAt($sketchId);
}
?>
