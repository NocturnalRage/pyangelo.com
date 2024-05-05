<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
  
    <h3 class="text-center">Before You Ask</h3>
    <p class="text-center">Have you searched the PyAngelo website? We've answered lots questions and have many video tutorials. The best part of searching is you get an instant response and can continue the conversation by adding a comment.</p>
    <hr />
    <?php include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php'; ?>
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <form method="post" action="/ask-the-teacher/create" class="form-horizontal">
          <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken'] ?>" />
          <div class="form-group<?= isset($errors['question_title']) ? ' has-error' : ''; ?>">
            <label for="question_title" class="control-label">Question Title:</label>
            <input type="text" name="question_title" id="question_title" class="form-control" placeholder="Question Title" value="<?= $this->esc($formVars['question_title'] ?? ''); ?>" maxlength="100" required autofocus />
            <?php if (isset($errors['question_title'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['question_title']); ?></div>
            <?php endif; ?>
          </div>

          <div class="form-group<?= isset($errors['question']) ? ' has-error' : ''; ?>">
            <label for="question" class="control-label">Question:</label>
            <textarea name="question" id="question" class="form-control tinymce" placeholder="Enter your question..." rows="10" /><?= $formVars['question'] ?? '' ?></textarea>
            <?php if (isset($errors['question'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['question']); ?></div>
            <?php endif; ?>
          </div>

          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-question" aria-hidden="true"></i> Submit My Question
            </button>
          </div>
        </form>
      </div><!-- col-md-6 -->
    </div><!-- row -->

<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
  </div><!-- container -->
  <script src="<?= mix('js/tinymce-default-config.js'); ?>"></script>
</body>
</html>
