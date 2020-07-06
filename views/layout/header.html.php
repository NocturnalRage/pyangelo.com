<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <meta name="description" content="<?= $this->esc($metaDescription) ?>">
  <meta name="author" content="PingSkills">
  <title><?= $this->esc($pageTitle); ?></title>
  <link rel="stylesheet" href="<?= mix('css/pyangelo.css'); ?>">
  <link href="//vjs.zencdn.net/5.8.8/video-js.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="/images/icons/pyangelo-favicon.png">
  <?php if (isset($sketch['sketch_id'])): ?>
  <!-- Set base URL for the sketch!
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <base href="/sketches/<?= $sketch['sketch_id'] ?>/" />
  <?php endif; ?>
  <script src="<?= mix('js/app.js'); ?>"></script>
  <!-- Brython
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/brython/3.8.8/brython.min.js" integrity="sha256-o/getzwHeAq4xcWJ350CFg+70KNoYxxdK8ikIp76hIM=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/brython/3.8.8/brython_stdlib.js" integrity="sha256-Gnrw9tIjrsXcZSCh/wos5Jrpn0bNVNFJuNJI9d71TDs=" crossorigin="anonymous"></script>

  <!-- Ace Editor
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.11/ace.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.11/mode-python.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.11/ext-language_tools.min.js"></script>

</head>
<?php if (!empty($sketch['sketch_id'])): ?>
<body onload="brython({debug:1, pythonpath:['/sketches/<?= $sketch['sketch_id'] ?>', '/brython/lib']})">
<?php else: ?>
<body>
<?php endif; ?>
