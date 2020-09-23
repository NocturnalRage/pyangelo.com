<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <h1>Favourite Questions</h1>
    <?php if ($questions) : ?>
      <?php include __DIR__ . '/questions-table.html.php'; ?>
    <?php else : ?>
      <p>You haven't marked any of the questions as your favourite yet :(</p>
    <?php endif; ?>

    <?php include __DIR__ . '/../layout/footer.html.php'; ?>
  </div><!-- container -->
</body>
</html>
