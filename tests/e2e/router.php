<?php
/**
 * Router for PHP's built-in server in the Playwright smoke tests.
 *
 * Files under public/ (the Vite build in public/build) are served as they
 * are. /fixture/<name> renders a real template from views/ with the data in
 * fixtures.php, so the pages load the real built scripts without a database.
 */
$root = dirname(__DIR__, 2);
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($path !== '/' && is_file($root . '/public' . $path)) {
  return false;
}

if (! preg_match('#^/fixture/([a-z0-9-]+)$#', $path, $match)) {
  http_response_code(404);
  echo 'Not found';
  return true;
}

require_once $root . '/vendor/autoload.php';
$fixtures = require __DIR__ . '/fixtures.php';
if (! isset($fixtures[$match[1]])) {
  http_response_code(404);
  echo 'Unknown fixture';
  return true;
}

// Templates read these; the Turnstile key is Cloudflare's always-pass test key.
$_ENV['TURNSTILE_SITE_KEY'] = '1x00000000000000000000AA';
$_SERVER['DOCUMENT_ROOT'] = $root . '/public';

[$view, $vars] = $fixtures[$match[1]]();
chdir($root);
$response = new Framework\Response('views');
$response->setView($view);
$response->setVars($vars);
echo $response->requireView();
return true;
