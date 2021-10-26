<?php
  include __DIR__ . '/../layout/header.html.php';
  include __DIR__ . '/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div id="instructions" class="col-md-12 text-center">
        <?php if (isset($skill)) : ?>
          <h1>Ready for the <?= $this->esc($skill['skill_name']); ?> Quiz?</h1>
        <?php else : ?>
          <h1>Ready for the <?= $this->esc($tutorial['title']); ?> Quiz?</h1>
        <?php endif ?>
        <p>Show us what you can do!</p>
        <h2><?= $quizInfo['question_count']; ?> questions</h2>
        <button id="startBtn" class="btn btn-success">Let's go</button>
      </div>
    </div><!-- row -->

    <div class="row">
      <div id="quiz" data-quiz-id="<?= $this->esc($quizInfo['quiz_id']); ?>" data-crsf-token="<?= $personInfo['crsfToken'] ?>" data-tutorial-slug="<?= $tutorial['slug'] ?>" class="col-md-12">
      </div>
    </div><!-- row -->

    <div class="row">
      <div id="hint" class="col-md-12"></div>
    </div><!-- row -->

    <div class="row add-bottom">
      <div id="progress" class="col-md-12">
        <button id="action" class="btn btn-lg btn-primary">Check Answer</button>
        <?php for ($i = $quizInfo['question_count'] - 1; $i >= 0; $i--) :?>
          <span id="dot<?= $this->esc($i); ?>" class="dot"></span>
        <?php endfor; ?>
      </div>
    </div><!-- row -->

    <div class="row">
      <div id="feedback" class="col-md-12"></div>
    </div><!-- row -->

    <?php include __DIR__ . '/../layout/footer.html.php'; ?>
  </div><!-- container -->
  <script src="<?= mix('js/quiz.js'); ?>"></script>
</body>
</html>
