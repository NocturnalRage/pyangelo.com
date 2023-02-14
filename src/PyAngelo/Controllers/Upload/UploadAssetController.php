<?php
namespace PyAngelo\Controllers\Upload;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\SketchRepository;

class UploadAssetController extends Controller {
  protected $sketchRepository;
  protected $maxSize;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    sketchRepository $sketchRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->sketchRepository = $sketchRepository;
    $this->maxSize = 8388608;
  }

  public function exec() {
    $this->response->setView('upload/upload-asset.json.php');
    $this->response->header('Content-Type: application/json');
    // Set default values for json response
    $status = 'success';
    $message = 'Upload attempt';
    $filetype = 'none';

    if (!$this->auth->loggedIn()) {
      $status = 'error';
      $message = 'You must be logged in to upload an asset!';
    }
    // Validate the CRSF token
    else if (!$this->auth->crsfTokenIsValid()) {
      $status = 'error';
      $message = 'You must upload files from the PyAngelo website!';
    }
    // Check sketchId has been passed
    else if (!isset($this->request->post['sketchId'])) {
      $status = 'error';
      $message = 'You must upload files for a sketch!';
    }
    // Check sketch exists in the database
    else if (!($sketch = $this->sketchRepository->getSketchById(
      $this->request->post['sketchId']
    ))) {
      $status = 'error';
      $message = 'You must upload files for a valid sketch!';
    }
    else if ($sketch['person_id'] != $this->auth->personId()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must be the owner of the sketch to upload files to it.'
      ));
      return $this->response;
    }
    else if (!isset($this->request->files['file'])) {
      $status = 'error';
      $message = 'No file was received!';
    }
    else if ($this->isAssetSizeZero($this->request->files['file'])) {
      $status = 'error';
      $message = 'File of type ' . $this->request->files['file']['type'] . ' was of size zero!';
    }
    else if ($this->isAssetTooLarge($this->request->files['file'])) {
      $status = 'error';
      $message = 'File too large. The file must be at most ' . $this->maxSize . ' bytes.';
    }
    else if (! $this->isAssetValidType($this->request->files['file'])) {
      $status = 'error';
      $message = 'Invalid file type (' . $this->request->files['file']['type'] . ').  File must be jpg, png, gif, wav, or mp3!';
    }
    if ($status == 'error') {
      $this->response->setVars(array(
        'status' => $status,
        'message' => $message,
        'filename' => 'No file uploaded',
        'filetype' => $filetype
      ));
      return $this->response;
    }

    $fullFileName = $this->moveFile($this->request->files['file'], $sketch);

    if ($fullFileName == -1) {
      $status = 'error';
      $message = 'The file already exists. Please rename this file before trying again!';
      $this->response->setVars(array(
        'status' => $status,
        'message' => $message,
        'filename' => $this->request->files['file']['name'],
        'filetype' => 'none'
      ));
      return $this->response;
    }

    if (!$fullFileName) {
      $status = 'error';
      $message = 'The image could not be uploaded!';
      $this->response->setVars(array(
        'status' => $status,
        'message' => $message,
        'filename' => $this->request->files['file']['name']
      ));
      $this->response->header("HTTP/1.1 400 Bad Request");
      return $this->response;
    }
    $this->sketchRepository->addSketchFile(
      $this->request->post['sketchId'],
      $fullFileName
    );

    $this->response->setVars(array(
      'status' => 'success',
      'message' => 'The file has been uploaded.',
      'filename' => $fullFileName,
      'filetype' => $this->request->files['file']['type']
    ));
    return $this->response;
  }

  private function isAssetSizeZero($assetInfo) {
    if ($assetInfo["size"] == 0) {
      return true;
    }
    return false;
  }

  private function isAssetTooLarge($assetInfo) {
    if ($assetInfo['size'] > $this->maxSize) {
      return true;
    }
    return false;
  }

  private function isAssetValidType($assetInfo) {
    if ($assetInfo['type'] != 'image/jpeg' && $assetInfo['type'] != 'image/png' && $assetInfo['type'] != 'image/gif' && $assetInfo['type'] != 'audio/mpeg' && $assetInfo['type'] != 'audio/wav') {
      return false;
    }
    return true;
  }

  private function moveFile($fileInfo, $sketch) {
    $baseDir =  'sketches/' . $sketch['person_id'] . '/' . $sketch['sketch_id'];
    $filename = pathinfo($fileInfo['name'], PATHINFO_FILENAME);
    $filetype = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
    $fullFileName = $filename . '.' . $filetype;
    $uploadFile = $baseDir . '/' . $fullFileName;

    if (file_exists($uploadFile)) {
      return -1;
    }

    if (!move_uploaded_file($fileInfo['tmp_name'], $uploadFile)) {
      return false;
    }
    return $fullFileName;
  }
}
