<?php
include __DIR__ . '/../layout/header.html.php';
include __DIR__ . '/../layout/navbar.html.php';
?>
  <div class="container">
    <?php include __DIR__ . '/latest-blog-comments.html.php'; ?>
    <hr />
    <?php include __DIR__ . '/latest-question-comments.html.php'; ?>
    <hr />
    <?php include __DIR__ . '/latest-lesson-comments.html.php'; ?>

    <?php include __DIR__ . '/../layout/footer.html.php'; ?>
  </div><!-- container -->
</body>
</html>
