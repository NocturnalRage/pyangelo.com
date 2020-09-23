<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <?php if ($personInfo['isAdmin']) : ?>
      <a href="/ask-the-teacher/<?= $this->esc($question['slug']) ?>/edit" class="btn btn-warning">
        <i class="fa fa-pencil-square-o"></i> Update Question</a>
      <a href="/ask-the-teacher/question-list" class="btn btn-primary">
        <i class="fa fa-question"></i> Question List</a>
    <?php endif; ?>
    <div class="text-center">
      <h1><?= $this->esc($question['question_title']) ?></h1>
      <h3>Category: <?= $this->esc($question['category_description']); ?></h3>
      <?php
        $lastUpdatedAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $question['updated_at'])->diffForHumans();
        $createdAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $question['created_at'])->diffForHumans();
        $answeredAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $question['answered_at'])->diffForHumans();
      ?>
      <p><em>Last updated <?= $this->esc($lastUpdatedAt) ?></em></p>
    </div>

  <div class="well">

    <!-- Question -->
    <div class="media">
      <div class="media-left">
        <img class="media-object" src="<?= $avatar->getAvatarUrl($question['questionee_email']) ?>" alt="<?= $this->esc($question['display_name']) ?>" />
      </div>
      <div class="media-body">
        <h4 class="media-heading"><?= $this->esc($question['display_name']) ?> <small><i>Asked <?= $this->esc($createdAt) ?></i></small></h4>
        <div><?= $purifier->purify($question['question']); ?></div>
      </div>
    </div>

    <hr />

    <!-- Answer -->
    <div class="media">
      <div class="media-left">
        <img class="media-object" src="<?= $avatar->getAvatarUrl($question['teacher_email']) ?>" alt="<?= $this->esc($question['teacher_display_name']) ?>" />
      </div>
      <div class="media-body">
        <h4 class="media-heading"><?= $this->esc($question['teacher_display_name']) ?> <small><i>Answered <?= $this->esc($answeredAt) ?></i></small></h4>
        <div><?= $purifier->purify($question['answer']); ?></div>
      </div>
      <hr />
    </div>

    <!-- Previous Next -->
    <div class="add-bottom">
      <?php if ($nextQuestion) : ?>
        <span class="pull-left">&larr;
          <a href="/ask-the-teacher/<?= $this->esc($nextQuestion["slug"]) ?>">
            <?= $this->esc($nextQuestion["question_title"]) ?>
          </a>
        </span>
      <?php endif; ?>
      <?php if ($previousQuestion) : ?>
        <span class="pull-right">
          <a href="/ask-the-teacher/<?= $this->esc($previousQuestion["slug"]) ?>">
            <?= $this->esc($previousQuestion["question_title"]) ?> &rarr;
          </a>
        </span>
      <?php endif; ?>
    </div>
  </div>
  <div class="col-md-4 text-center add-bottom">
    <a id="alertStatus"
       class="btn <?= $alertUser ? 'btn-info' : 'btn-primary' ?>"
       href="#"
       data-question-id="<?= $this->esc($question['question_id']); ?>"
       data-crsf-token="<?= $personInfo['crsfToken']; ?>"
       aria-label="Toggle Alert">
      <i class="fa fa-bookmark" aria-hidden="true"></i>
      <?= $alertUser ? 'Stop notifications' : 'Notify me of updates' ?>
    </a>
  </div><!-- md-col-12 add-bottom -->
  <div class="col-md-4 text-center add-bottom">
    <a id="favouriteStatus"
       class="btn <?= $question['favourited'] ? 'btn-primary' : 'btn-default' ?>"
       href="#"
       data-question-id="<?= $this->esc($question['question_id']); ?>"
       data-crsf-token="<?= $personInfo['crsfToken']; ?>"
       aria-label="Toggle favourited">
      <i class="fa fa-star" aria-hidden="true"></i>
      <?= $question['favourited'] ? 'Favourite' : 'Add to Favourites' ?>
    </a>
  </div><!-- md-col-12 add-bottom -->
  <div class="col-md-4 text-center add-bottom">
    <a href="/ask-the-teacher" class="btn btn-info">
      <i class="fa fa-reply"></i> Back to Questions
    </a>
  </div><!-- md-col-12 add-bottom -->

    <?php
      include __DIR__ . '/question-comments.html.php';
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
    ?>
  </div><!-- container -->
  <script src="<?= mix('js/notify.min.js'); ?>"></script>
  <script src="<?= mix('js/questionAlert.js'); ?>"></script>
  <script src="<?= mix('js/questionFavourite.js'); ?>"></script>
  <script src="<?= mix('js/questionComments.js'); ?>"></script>
  <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
  <script type="text/javascript">
tinymce.init({
  selector: 'textarea.tinymce',
  toolbar_items_size: 'small',
  plugins: "link, image, hr, lists",
  relative_urls : false,
  browser_spellcheck: true,
  toolbar: "undo redo | formats formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink image media | hr blockquote",
  image_class_list : [ {title: 'Responsive Image', value: 'img-responsive' } ],
  image_caption: true,
  menubar: false,
  statusbar: false
});
  </script>
</body>
</html>
