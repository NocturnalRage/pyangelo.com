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

<script src="<?= mix('js/SkulptSketchCanvasOnly.js'); ?>"></script>
</body>
</html>
