<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="text-center">
      <h1>Answer the Question</h1>
      <h2><?= $this->esc($question['question_title']); ?></h2>
      <p><?= $this->esc($question['display_name'] . ' (' . $question['questionee_email'] . ')') ?></p>
     </div>
    <hr />
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php';
    ?>
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <form id="questionForm"
              method="post"
              action="/ask-the-teacher/<?= $this->esc($question['slug']); ?>/update"
        >
          <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
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

          <div class="form-group<?= isset($errors['answer']) ? ' has-error' : ''; ?>">
            <label for="answer" class="control-label">Answer:</label>
            <textarea name="answer" id="answer" class="form-control tinymce" placeholder="Answer the question..." rows="10" /><?= $formVars['answer'] ?? '' ?></textarea>
            <?php if (isset($errors['answer'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['answer']); ?></div>
            <?php endif; ?>
          </div>

          <div class="form-group<?= isset($errors['question_type_id']) ? ' has-error' : ''; ?>">
            <label for="question_type_id" class="control-label">Question type:</label>
            <select id="question_type_id" name="question_type_id" class="form-control">
            <?php foreach ($questionTypes as $questionType): ?>
              <option <?php if ($questionType['question_type_id'] == ($formVars['question_type_id'] ?? '')) echo 'selected'; ?> value="<?= $this->esc($questionType['question_type_id']); ?>"><?= $this->esc($questionType['description']); ?></option>
            <?php endforeach; ?>
            </select>
            <?php if (isset($errors['question_type_id'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['question_type_id']); ?></div>
            <?php endif; ?>
          </div>

          <div class="form-group">
            <div class="col-md-6 col-md-offset-4">
              <button type="submit" class="btn btn-primary">
                <i class="fa fa-reply" aria-hidden="true"></i> Answer The Question
              </button>
            </div>
          </div>
        </form>
      </div><!-- col-md-8 col-md-offset-2 -->
    </div><!-- row -->

<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
  </div><!-- container -->
  <script src="<?= mix('js/tinymce-default-config.js'); ?>"></script>
</body>
</html>
