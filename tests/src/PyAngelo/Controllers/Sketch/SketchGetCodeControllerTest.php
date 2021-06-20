<?php
namespace Tests\src\PyAngelo\Controllers\Sketch;

use PHPUnit\Framework\TestCase;
use Mockery;
use Dotenv\Dotenv;
use Framework\Request;
use Framework\Response;
use PyAngelo\Repositories\SketchRepository;
use PyAngelo\Controllers\Sketch\SketchGetCodeController;

class SketchGetCodeControllerTest extends TestCase {
  public function setUp(): void {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../../../', '.env.test');
    $dotenv->load();
    $this->appDir = $_ENV['APPLICATION_DIRECTORY'];;
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->sketchRepository = Mockery::mock('PyAngelo\Repositories\SketchRepository');
    $this->controller = new SketchGetCodeController (
      $this->request,
      $this->response,
      $this->auth,
      $this->sketchRepository,
      $this->appDir
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testWhenNoSlug() {
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/code.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must select a sketch to fetch.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testSketchNotInDatabase() {
    $sketchId = 101;
    $program = 'canvas.background()';
    $this->request->get['sketchId'] = $sketchId;
    $this->sketchRepository->shouldReceive('getSketchFiles')->once()->with($sketchId)->andReturn();

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/code.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You must select a valid sketch to fetch.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testSuccess() {
    $basepath = $this->appDir . "/public/sketches/0/0";
    $filename = $basepath . "/main.py";
    $program = "canvas.background()";
    if (! file_exists($basepath)) {
      mkdir($basepath, 0750, true);
    }
    file_put_contents($filename, $program);

    $ownerId = 101;
    $personId = $ownerId;
    $sketchFiles = [
      [
        'person_id' => 0,
        'file_id' => 1,
        'sketch_id' => 0,
        'filename' => 'main.py'
      ]
    ];
    $sketchId = 101;
    $program = 'canvas.background()';
    $this->request->get['sketchId'] = $sketchId;
    $this->sketchRepository->shouldReceive('getSketchFiles')->once()->with($sketchId)->andReturn($sketchFiles);

    $response = new Response('views');
    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'sketch/code.json.php';
    $expectedStatus = 'success';
    $expectedMessage = "files retrieved";
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
    $filename = $basepath . '/main.py';
    $savedProgram = file_get_contents($filename);
    $this->assertSame($savedProgram, $program);
    unlink($filename);
  }
}
?>
