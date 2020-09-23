<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <h1>Coding Questions on <?= $this->esc($category['description']) ?></h1>
    <?php include __DIR__ . '/questions-category-table.html.php'; ?>
    <hr />
    <?php include __DIR__ . '/categories.html.php'; ?>
    <?php include __DIR__ . '/../layout/footer.html.php'; ?>
  </div><!-- container -->
</body>
</html>
