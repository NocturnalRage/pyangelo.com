#!/usr/bin/php
<?php
namespace Framework\Cron;

require(dirname(__FILE__) . '/../../../vendor/autoload.php');
require(dirname(__FILE__) . '/../../../config/services.php');

# Load environment variables using dotenv
$dotenv = $di->get('dotenv');
$dotenv->load();

$lockFile = '/srv/http/pyangelo.com/src/Framework/Cron/SendAutoresponders.lock';
$fp = fopen($lockFile, "r+");
if (flock($fp, LOCK_EX | LOCK_NB)) {  // acquire an exclusive lock
  ftruncate($fp, 0);      // truncate file
  fwrite($fp, getmypid());

  $autoresponders = $di->get('autoresponders');
  $autoresponders->sendOutstanding();

  fflush($fp);            // flush output before releasing the lock
  flock($fp, LOCK_UN);    // release the lock
}
else {
  echo "SendAutoresponders.php: Couldn't get the lock!\n";
}
fclose($fp);
