<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
  <h1><?= $this->esc($category['category']) ?></h1>
    <?php if ($personInfo['isAdmin']) : ?>
    <a href="/categories/<?= $this->esc($category['category_slug']) ?>/sort" class="btn btn-warning">
      <i class="fa fa-sort"></i> Sort Tutorials in <?= $this->esc($category['category']) ?> Category</a>
    <?php endif; ?>
    <hr />
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . 'tutorials.html.php';
    ?>
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
    ?>
  </div><!-- container -->
</body>
</html>
