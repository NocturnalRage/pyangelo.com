<?php
namespace PyAngelo\Utilities;

class SketchFiles {
  const DEFAULT_MAIN_FILE = "main.py";
  const DEFAULT_MAIN_CODE = <<<'ENDDEFAULTMAINCODE'
ENDDEFAULTMAINCODE;

  public function __construct(string $appDir) {
    $this->appDir = $appDir;
  }

  public function createNewMain($personId, $sketchId) {
    $basePath = $this->appDir . '/public/sketches/' . $personId . '/' . $sketchId;
    if (! file_exists($basePath)) {
      mkdir($basePath, 0750, true);
    }
    $filename = $basePath . '/main.py';
    file_put_contents($filename, self::DEFAULT_MAIN_CODE);
  }

  public function createFile($sketch, $filename) {
    touch($this->appDir . '/public/sketches/' . $sketch['person_id'] . '/' . $sketch['sketch_id'] . '/' . $filename);
  }
  
  public function doesFileExist($sketch, $filename) {
    return file_exists($this->appDir . '/public/sketches/' . $sketch['person_id'] . '/' . $sketch['sketch_id'] . '/' . $filename); // something like that perhaps?!
  }
  
  public function deleteFile($sketch, $filename) {
    unlink($this->appDir . '/public/sketches/' . $sketch['person_id'] . '/' . $sketch['sketch_id'] . '/' . $filename);
    // does that work?? srsly?
  }

  public function saveCode($sketch, $filename, $code) {
    $basePath = $this->appDir . '/public/sketches/' . $sketch['person_id'] . '/' . $sketch['sketch_id'];
    if (! file_exists($basePath)) {
      mkdir($basePath, 0750, true);
    }
    $fullFilename = $basePath . '/' . $filename;
    $returnValue = file_put_contents($fullFilename, $code);
  }

  public function forkSketch($origSketch, $personId, $newSketchId, $sketchFiles) {
    $src = $this->appDir . '/public/sketches/' . $origSketch['person_id'] . '/' . $origSketch['sketch_id'];
    $dest = $this->appDir . '/public/sketches/' . $personId . '/' . $newSketchId;
    mkdir($dest, 0750, true);
    foreach ($sketchFiles as $file) {
      $srcFile = $src . '/' . $file['filename'];
      $destFile = $dest . '/' . $file['filename'];
      copy($srcFile, $destFile);
    }
  }
}
?>
