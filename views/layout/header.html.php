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
  <link rel="icon" type="image/png" href="/images/icons/pyangelo-favicon.png">
  <?php if (isset($sketch['sketch_id'])): ?>
  <!-- Set base URL for the sketch!
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <base href="/sketches/<?= $sketch['person_id'] ?>/<?= $sketch['sketch_id'] ?>/" />

  <!-- Ace Editor
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.11/ace.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.11/mode-python.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.11/ext-language_tools.min.js"></script>

  <!-- Skulpt Files
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <script src="<?= mix('js/skulpt.min.js'); ?>"></script>
  <script src="<?= mix('js/skulpt-stdlib.js'); ?>"></script>

  <?php endif; ?>

  <script src="<?= mix('js/app.js'); ?>"></script>

</head>
<body>
