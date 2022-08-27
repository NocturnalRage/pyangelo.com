<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1 class="text-center">PyAngelo Editor</h1>
    </div><!-- row -->
  </div><!-- container -->
  <div class="container-fluid">
    <?php
      include 'sketch-split-editor-console.html.php';
      include 'sketch-debug-table.html.php';
      include 'sketch-output.html.php';
      include 'sketch-turtle.html.php';
      include 'sketch-buttons.html.php';
    ?>
    <script src="<?= mix('js/editor.js'); ?>"></script>
  </div><!-- container-fluid -->

  <div class="container">
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
    ?>
  </div><!-- container -->
</body>
</html>
