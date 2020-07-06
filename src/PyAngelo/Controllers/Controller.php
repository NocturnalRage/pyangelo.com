<?php
namespace PyAngelo\Controllers;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;

abstract class Controller {
  protected $request;
  protected $response;
  protected $auth;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth
  ) {
    $this->request = $request;
    $this->response = $response;
    $this->auth = $auth;
  }

  abstract public function exec();

  public function addVar($varName) {
    if (isset($this->request->session[$varName])) {
      $this->response->addVars(array(
        $varName => $this->request->session[$varName]
      ));
      unset($this->request->session[$varName]);
    }
  }

  public function flash($message, $messageType = 'info') {
    $_SESSION['flash'] = [
      'message' => $message,
      'type' => $messageType
    ];
  }
}
