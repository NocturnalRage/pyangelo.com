<?php
namespace PyAngelo\Utilities;

class SketchFiles {
  const DEFAULT_MAIN_FILE = "main.py";
  const DEFAULT_MAIN_CODE = "canvas.background(255, 255, 0)";

  public function __construct(string $appDir) {
    $this->appDir = $appDir;
  }

  public function createNewMain($sketchId) {
    $basePath = $this->appDir . '/public/sketches/' . $sketchId;
    if (! file_exists($basePath)) {
      mkdir($basePath, 0750, true);
    }
    $filename = $basePath . '/main.py';
    file_put_contents($filename, self::DEFAULT_MAIN_CODE);
  }

  public function createFile($sketchId, $filename) {
    touch($this->appDir . '/public/sketches/' . $sketchId . '/' . $filename);
  }

  public function saveCode($sketchId, $filename, $code) {
    $basePath = $this->appDir . '/public/sketches/' . $sketchId;
    if (! file_exists($basePath)) {
      mkdir($basePath, 0750, true);
    }
    $fullFilename = $basePath . '/' . $filename;
    $returnValue = file_put_contents($fullFilename, $code);
  }

  public function forkSketch($origSketchId, $newSketchId, $sketchFiles) {
    $src = $this->appDir . '/public/sketches/' . $origSketchId;
    $dest = $this->appDir . '/public/sketches/' . $newSketchId;
    mkdir($dest, 0750, true);
    foreach ($sketchFiles as $file) {
      $srcFile = $src . '/' . $file['filename'];
      $destFile = $dest . '/' . $file['filename'];
      copy($srcFile, $destFile);
    }
  }
}
?>
