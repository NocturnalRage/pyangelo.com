<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <h1>Unanswered Questions</h1>
    <?php include __DIR__ . '/../layout/flash.html.php'; ?>
    <?php
      include __DIR__ . '/question-list-table.html.php';
      include __DIR__ . '/../layout/footer.html.php';
    ?>
  </div><!-- container -->
</body>
</html>
