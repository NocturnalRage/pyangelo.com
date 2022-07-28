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
$controllerAndAction = explode('@', $match['target']);
$controllerName = $controllerAndAction[0];
if (count($controllerAndAction) == 1) {
  $action = 'exec';
} else {
  $action = $controllerAndAction[1];
}
$controller = $di->newInstance($controllerName);
if (method_exists($controller, $action)) {
  $response = $controller->$action();
  $response->send();
}
else {
  throw new \Exception("$controllerName does not support $action");
}

?>
