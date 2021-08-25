<?php
  include __DIR__ . '/../layout/header.html.php';
  include __DIR__ . '/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div id="instructions" class="col-md-12 text-center">
        <h1>Ready for the <?= $this->esc($tutorial['title']); ?> Quiz?</h1>
        <p>Show us what you can do!</p>
        <h2><?= $tutorialQuizInfo['question_count']; ?> questions</h2>
        <button id="startBtn" class="btn btn-success">Let's go</button>
      </div>
    </div><!-- row -->

    <div class="row">
      <div id="quiz" data-tutorial-quiz-id="<?= $this->esc($tutorialQuizInfo['tutorial_quiz_id']); ?>" data-crsf-token="<?= $personInfo['crsfToken'] ?>" class="col-md-12">
      </div>
    </div><!-- row -->

    <div class="row">
      <div id="hint" class="col-md-12"></div>
    </div><!-- row -->

    <div class="row add-bottom">
      <div id="progress" class="col-md-12">
        <button id="action" class="btn btn-lg btn-primary">Check Answer</button>
        <?php for ($i = $tutorialQuizInfo['question_count'] - 1; $i >= 0; $i--) :?>
          <span id="dot<?= $this->esc($i); ?>" class="dot"></span>
        <?php endfor; ?>
      </div>
    </div><!-- row -->

    <div class="row">
      <div id="feedback" class="col-md-12"></div>
    </div><!-- row -->

    <?php include __DIR__ . '/../layout/footer.html.php'; ?>
  </div><!-- container -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.core.min.js" integrity="sha512-+wqa4/aOL4vwhL637A4p+6epqhUHDqpEfYJa1+PlzlMf908CSOb3xtIeKwZWh5Q0Q/R2nYUKxH4vvcQg+k8KKQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.4.0/dist/confetti.browser.min.js"></script>
  <script src="<?= mix('js/quiz.js'); ?>"></script>
</body>
</html>
