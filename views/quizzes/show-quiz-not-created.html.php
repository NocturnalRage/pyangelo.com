<?php
  include __DIR__ . '/../layout/header.html.php';
  include __DIR__ . '/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12 text-center">
        <h1><?= $this->esc($tutorial['title']); ?> Tutorial Quiz</h1>
      </div>
    </div><!-- row -->
<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../lessons/skills-table.html.php';
?>
    <?php include __DIR__ . '/../layout/footer.html.php'; ?>
  </div><!-- container -->
</body>
</html>
