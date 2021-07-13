<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <meta name="description" content="<?= $this->esc($metaDescription) ?>">
  <meta name="author" content="Nocturnal Rage">
  <title><?= $this->esc($pageTitle); ?></title>
  <link rel="stylesheet" href="<?= mix('css/pyangelo.css'); ?>">
  <link href="//vjs.zencdn.net/5.8.8/video-js.css" rel="stylesheet">

  <link rel="apple-touch-icon" sizes="180x180" href="/images/icons/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/images/icons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/images/icons/favicon-16x16.png">
  <link rel="manifest" href="/images/icons/site.webmanifest">
  <link rel="mask-icon" href="/images/icons/safari-pinned-tab.svg" color="#5bbad5">
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="theme-color" content="#ffffff">

  <?php if (isset($sketch['sketch_id'])): ?>
  <!-- Set base URL for the sketch!
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <base href="/sketches/<?= $sketch['person_id'] ?>/<?= $sketch['sketch_id'] ?>/" />

  <!-- Ace Editor
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/mode-python.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ext-language_tools.min.js"></script>

  <!-- Skulpt Files
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <script src="<?= mix('js/skulpt.min.js'); ?>"></script>
  <script src="<?= mix('js/skulpt-stdlib.js'); ?>"></script>

  <?php endif; ?>

  <script src="<?= mix('js/app.js'); ?>"></script>

</head>
<body>
