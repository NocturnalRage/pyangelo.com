<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <?php
        include __DIR__ . DIRECTORY_SEPARATOR . 'reference-menu.html.php';
      ?>
      <div class="col-md-9">
        <h1>Reference</h1>
        <?php
          include __DIR__ . DIRECTORY_SEPARATOR . 'canvas.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . 'basic-shapes.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . 'vertex.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . 'colour.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . 'keyboard.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . 'typography.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . 'images.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . 'transformation.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . 'console.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . 'sprites.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . 'animation.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . 'sounds.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . 'variables.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . 'maths.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . 'constants.html.php';
        ?>
      </div><!-- col-md-9 -->
    </div><!-- row -->
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
    ?>
  </div><!-- container -->
</body>
</html>
