<?php
require(dirname(__FILE__) . '/../vendor/autoload.php');
require(dirname(__FILE__) . '/../config/services.php');

session_start();

# Load environment variables using dotenv
$dotenv = $di->get('dotenv');
$dotenv->load();

$router = $di->get('router');
$match = $router->match();
if (isset($match['params'])) {
  $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
  foreach ($match['params'] as $key => $param) {
    if ($requestMethod == 'POST') {
      $_POST[$key] = $param;
    }
    else {
      $_GET[$key] = $param;
    }
  }
}

if (!$match) {
  $match = [];
  $match['target'] = 'PageNotFoundController';
}

$controller = $di->newInstance($match['target']);
$response = $controller->exec();
$response->send();

?>
