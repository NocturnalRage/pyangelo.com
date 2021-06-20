<?php
namespace Tests\src\PyAngelo\Utilities;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use PyAngelo\Utilities\SketchFiles;

class SketchFilesTest extends TestCase {
  public function setUp(): void {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../../', '.env.test');
    $dotenv->load();
    $this->appdir = $_ENV['APPLICATION_DIRECTORY'];;
    $this->sketchFiles = New SketchFiles($this->appdir);
  }

  public function testCreateNewMain() {
    $sketchId = 0;
    $personId = 0;
    $this->sketchFiles->createNewMain($personId, $sketchId);
    $filename = $this->appdir . '/public/sketches/' . $personId . '/' . $sketchId . '/' . SketchFiles::DEFAULT_MAIN_FILE;
    $code = file_get_contents($filename);
    unlink($filename);
    $this->assertSame(SketchFiles::DEFAULT_MAIN_CODE, $code);
  }

  public function testSaveCode() {
    $sketchId = 0;
    $personId = 0;
    $sketch = [
      'sketch_id' => $sketchId,
      'person_id' => $personId
    ];
    $code = SketchFiles::DEFAULT_MAIN_CODE;
    $filename = 'test.py';

    $fullFilename = $this->appdir . '/public/sketches/' . $personId . '/' . $sketchId . '/' . $filename;
    $this->sketchFiles->saveCode($sketch, $filename, $code);
    $lookupcode = file_get_contents($fullFilename);
    unlink($fullFilename);
    $this->assertSame($code, $lookupcode);
  }
}
?>
