<?php
namespace PyAngelo\Controllers;

use DateTime;
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
    if (isset($_SESSION[$varName])) {
      $this->response->addVars(array(
        $varName => $_SESSION[$varName]
      ));
      unset($_SESSION[$varName]);
    }
  }

  public function flash($message, $messageType = 'info') {
    $_SESSION['flash'] = [
      'message' => $message,
      'type' => $messageType
    ];
  }

  public function logMessage($message, $logLevel) {
    if (! in_array($logLevel, ['DEBUG', 'INFO', 'WARNING', 'ERROR']))
      $logLevel = 'INFO';

    $date = new DateTime();
    $logDate = $date->format("y-m-d h:i:s");

    $message = '[' . $logDate . '] ' . $logLevel . ': ' . $message . PHP_EOL;

    file_put_contents($_ENV['APPLICATION_LOG_FILE'], $message, FILE_APPEND);
  }
}
