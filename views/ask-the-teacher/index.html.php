<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <?php if ($personInfo['isAdmin']) : ?>
      <a href="/ask-the-teacher/question-list" class="btn btn-primary">
        <i class="fa fa-question"></i> Question List
      </a>
    <?php endif; ?>
    <h1>Coding Questions</h1>
    <p>
    Here are our most recently updated coding questions.
    If you can't find what you're looking for then you can
    <a href="/ask-the-teacher/ask">ask your own question</a>.
    </p>
    <?php include __DIR__ . '/questions-table.html.php'; ?>
    <div class="row add-bottom">
      <?php if (count($questions) < 1) : ?>
        <span class="pull-left">&larr;
          <a href="/ask-the-teacher">
            View 1st Page
          </a>
        </span>
      <?php elseif ($pageNo > 1) : ?>
        <span class="pull-left">&larr;
          <a href="/ask-the-teacher?pageNo=<?= $pageNo-1 ?>">
            Page <?= $this->esc($pageNo-1) ?>
          </a>
        </span>
      <?php endif; ?>
      <?php if (count($questions) == $questionsPerPage) : ?>
        <span class="pull-right">
          <a href="/ask-the-teacher?pageNo=<?= ($pageNo+1) ?>">
            Page <?= $this->esc($pageNo+1) ?>&rarr;
          </a>
        </span>
      <?php endif; ?>
    </div>
    <?php include __DIR__ . '/categories.html.php'; ?>
    <?php include __DIR__ . '/../layout/footer.html.php'; ?>
  </div><!-- container -->
</body>
</html>
