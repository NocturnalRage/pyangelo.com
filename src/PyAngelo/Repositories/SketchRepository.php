<?php
namespace PyAngelo\Repositories;

interface SketchRepository {

  public function getSketches($personId);

  public function getAllSketches();
 
  public function getSketchById($sketchId);

  public function getDeletedSketchById($sketchId);

  public function getSketchByPersonAndTutorial($personId, $lessonId);

  public function getSketchByPersonAndLesson($personId, $lessonId);

  public function getSketchFiles($sketchId);

  public function createNewSketch($personId, $title, $collectionId, $lessonId = NULL);

  public function deleteSketch($sketchId);

  public function restoreSketch($sketchId);

  public function addSketchFile($sketchId, $filename);
  
  public function deleteSketchFile($sketchId, $filename);

  public function forkSketch($sketchId, $personId, $title, $lessonId = NULL, $tutorialId = NULL, $layout = 'cols');

  public function renameSketch($sketchId, $title);

  public function updateSketchUpdatedAt($sketchId);

  public function updateSketchLayout($sketchId, $layout);

  public function createNewCollection($personId, $title);

  public function getCollections($personId);

  public function addSketchToCollection($sketchId, $collectionId);

  public function removeSketchFromAllCollections($sketchId);

  public function getCollectionById($collectionId);

  public function getCollectionSketches($collectionId);

  public function renameCollection($collectionId, $title);
}
?>
