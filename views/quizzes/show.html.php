<?php
  include __DIR__ . '/../layout/header.html.php';
  include __DIR__ . '/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12 text-center">
        <h1><?= $this->esc($tutorial['title']); ?> Tutorial Quiz</h1>
        <p>There are <?= $tutorialQuizInfo['question_count']; ?> questions in this quiz</p>
        <button id="startBtn" class="btn btn-success">Start Now</button>
      </div>
    </div><!-- row -->

    <div class="row">
      <div id="quiz" data-tutorial-quiz-id="<?= $this->esc($tutorialQuizInfo['tutorial_quiz_id']); ?>" data-crsf-token="<?= $personInfo['crsfToken'] ?>" class="col-md-12">
      </div>
    </div><!-- row -->

    <div class="row">
      <div id="hint" class="col-md-12"></div>
    </div><!-- row -->

    <div class="row">
      <div id="feedback" class="col-md-12"></div>
    </div><!-- row -->

    <div class="row">
      <div class="col-md-12">
        <button id="action" class="btn btn-lg btn-primary">Check Answer</button>
      </div>
    </div><!-- row -->

    <div class="row">
      <div id="progress" class="col-md-12">
      </div>
    </div><!-- row -->
    <?php include __DIR__ . '/../layout/footer.html.php'; ?>
  </div><!-- container -->
  <script src="<?= mix('js/quiz.js'); ?>"></script>
</body>
</html>
