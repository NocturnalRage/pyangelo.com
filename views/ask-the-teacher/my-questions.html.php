<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <?php if ($unansweredQuestions) : ?>
      <h1>Unanswered Questions</h1>
      <?php include __DIR__ . '/unanswered-questions-table.html.php'; ?>
    <?php endif; ?>

    <h1>My Questions</h1>
    <?php if ($questions) : ?>
      <?php include __DIR__ . '/questions-table.html.php'; ?>
    <?php else : ?>
      <p>You don't have a question that we've answered yet!</p>
    <?php endif; ?>

    <?php include __DIR__ . '/../layout/footer.html.php'; ?>
  </div><!-- container -->
</body>
</html>
