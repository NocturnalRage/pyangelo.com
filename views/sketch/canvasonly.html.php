<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
?>
  <div class="container-fluid">

<?php
include 'sketch-editor.html.php';
include 'sketch-console.html.php';
include 'sketch-output.html.php';
include 'sketch-buttons.html.php';
?>

  </div><!-- container-fluid -->

<?= viteScript('resources/assets/js/SkulptSketchCanvasOnly.js'); ?>
</body>
</html>
