<?php
namespace PyAngelo\Repositories;

interface SketchRepository {

  public function getSketches($personId);

  public function getAllSketches();
 
  public function getSketchById($sketchId);

  public function getSketchFiles($sketchId);

  public function createNewSketch($personId, $title);

  public function addSketchFile($sketchId, $filename);

  public function forkSketch($sketchId, $personId, $title);

  public function renameSketch($sketchId, $title);
}
?>
