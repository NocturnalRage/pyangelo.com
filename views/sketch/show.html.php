<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">

<?php
include 'sketch-title.html.php';
include 'sketch-editor.html.php';
include 'sketch-console.html.php';
include 'sketch-output.html.php';
include 'sketch-buttons.html.php';
include 'sketch-upload.html.php';
?>

  </div><!-- container -->

<script src="<?= mix('js/PyAngeloSetup.js'); ?>"></script>
<script src="<?= mix('js/editor.js'); ?>"></script>
<script>
loadCode();
</script>

<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
</body>
</html>
