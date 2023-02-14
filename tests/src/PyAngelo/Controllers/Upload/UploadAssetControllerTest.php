<?php
namespace Tests\src\PyAngelo\Controllers\Upload;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Upload\UploadAssetController;

class UploadAssetControllerTest extends TestCase {
  protected $request;
  protected $response;
  protected $auth;
  protected $sketchRepository;
  protected $controller;

  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->sketchRepository = Mockery::mock('PyAngelo\Repositories\SketchRepository');
    $this->controller = new UploadAssetController (
      $this->request,
      $this->response,
      $this->auth,
      $this->sketchRepository
    );
  }
  protected function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Upload\UploadAssetController');
  }

  public function testUploadAssetControllerNotLoggedIn() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(
      array('header', 'Content-Type: application/json'),
    );
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must be logged in to upload an asset!', $responseVars['message']);
  }

  public function testUploadAssetControllerNoCrsfToken() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(
      array('header', 'Content-Type: application/json'),
    );
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must upload files from the PyAngelo website!', $responseVars['message']);
  }

  public function testUploadAssetControllerNoSketchId() {
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(
      array('header', 'Content-Type: application/json'),
    );
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must upload files for a sketch!', $responseVars['message']);
  }

  public function testUploadAssetControllerNoSketchIdInDatabase() {
    $sketchId = 101;
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn(NULL);
    $this->request->post['sketchId'] = $sketchId;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(
      array('header', 'Content-Type: application/json'),
    );
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('You must upload files for a valid sketch!', $responseVars['message']);
  }

  public function testUploadAssetControllerNoAsset() {
    $sketchId = 101;
    $personId = 11;
    $sketch = [
      'sketch_id' => $sketchId,
      'person_id' => $personId
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);
    $this->request->post['sketchId'] = $sketchId;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(
      array('header', 'Content-Type: application/json'),
    );
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('No file was received!', $responseVars['message']);
  }

  public function testUploadAssetControllerAssetTooLarge() {
    $sketchId = 101;
    $personId = 11;
    $sketch = [
      'sketch_id' => $sketchId,
      'person_id' => $personId
    ];
    $fileAsset = [
      'size' => 8388609,
      'type' => 'image/jpeg'
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);
    $this->request->post['sketchId'] = $sketchId;
    $this->request->files['file'] = $fileAsset;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(
      array('header', 'Content-Type: application/json'),
    );
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('File too large. The file must be at most 8388608 bytes.', $responseVars['message']);
  }

  public function testUploadAssetControllerAssetSizeZero() {
    $sketchId = 101;
    $personId = 11;
    $sketch = [
      'sketch_id' => $sketchId,
      'person_id' => $personId
    ];
    $fileAsset = [
      'size' => 0,
      'type' => 'image/jpeg'
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);
    $this->request->post['sketchId'] = $sketchId;
    $this->request->files['file'] = $fileAsset;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(
      array('header', 'Content-Type: application/json'),
    );
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('File of type image/jpeg was of size zero!', $responseVars['message']);
  }

  public function testUploadAssetControllerAssetInvalidType() {
    $sketchId = 101;
    $personId = 11;
    $sketch = [
      'sketch_id' => $sketchId,
      'person_id' => $personId
    ];
    $fileAsset = [
      'size' => 8388608,
      'type' => 'image/mp4'
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);
    $this->request->post['sketchId'] = $sketchId;
    $this->request->files['file'] = $fileAsset;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(
      array('header', 'Content-Type: application/json'),
    );
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('Invalid file type (image/mp4).  File must be jpg, png, gif, wav, or mp3!', $responseVars['message']);
  }

  public function testUploadAssetControllerCouldNotMove() {
    $sketchId = 101;
    $personId = 11;
    $sketch = [
      'sketch_id' => $sketchId,
      'person_id' => $personId
    ];
    $fileAsset = [
      'size' => 1048576,
      'type' => 'image/jpeg',
      'name' => 'test.jpg',
      'tmp_name' => 'tmp.jpg'
    ];
    $this->auth->shouldReceive('loggedIn')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('crsfTokenIsValid')->once()->with()->andReturn(true);
    $this->auth->shouldReceive('personId')->once()->with()->andReturn($personId);
    $this->sketchRepository->shouldReceive('getSketchById')->once()->with($sketchId)->andReturn($sketch);
    $this->request->post['sketchId'] = $sketchId;
    $this->request->files['file'] = $fileAsset;

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedHeaders = array(
      array('header', 'Content-Type: application/json'),
      array('header', 'HTTP/1.1 400 Bad Request')
    );
    $this->assertSame($expectedHeaders, $response->getHeaders());
    $this->assertSame('error', $responseVars['status']);
    $this->assertSame('The image could not be uploaded!', $responseVars['message']);
  }
}
?>
