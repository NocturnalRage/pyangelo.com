<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-4">
        <img src="/uploads/images/tutorials/<?= $this->esc($tutorial['thumbnail']); ?>" 
             alt="<?= $this->esc($tutorial['title']); ?>" class="img-responsive featuredThumbnail"
        >
      </div>
      <div class="col-md-8">
        <h3><?= $this->esc($tutorial['title']); ?></h3>
        <h4><span class="label label-<?= $this->esc(strtolower($tutorial['level'])); ?>"><?= $this->esc($tutorial['level']); ?></span></h4>
        <div class="tutorial-description add-bottom">
          <?= $this->esc($tutorial['description']); ?>
        </div>
        <?php if (! empty($tutorial['pdf'])) : ?>
          <div class="tutorial-pdf add-bottom pull-right">
            <a href="/uploads/pdf/tutorials/<?= $this->esc($tutorial['pdf']); ?>" target="_blank" class="btn btn-lg btn-primary">
              <i class="fa fa-file-pdf-o"></i> <strong>Download Tutorial PDF</strong></a>
          </div>
        <?php endif; ?>
        <?php if ($personInfo['isAdmin']) : ?>
          <div class="add-bottom pull-left">
            <a href="/tutorials/<?= $this->esc($tutorial['slug']); ?>/edit" class="btn btn-warning">
              <i class="fa fa-pencil-square-o"></i> Edit</a>
          </div>
        <?php endif; ?>
      </div>
    </div><!-- row -->
<?php
include __DIR__ . DIRECTORY_SEPARATOR . 'tutorial-info-panel.html.php';
?>
    <div class="row">
      <div class="col-md-12">
        <h3>Tutorial Lessons</h3>
        <?php if ($personInfo['isAdmin']) : ?>
          <a href="/tutorials/<?= $this->esc($tutorial['slug']); ?>/lessons/new" class="btn btn-warning">
            <i class="fa fa-plus"></i> New Lesson</a>
          <a href="/tutorials/<?= $this->esc($tutorial['slug']); ?>/lessons/sort" class="btn btn-warning">
            <i class="fa fa-sort"></i> Sort Lessons</a>
        <?php endif; ?>
      </div>
    </div><!-- row -->
<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../lessons/lessons-table.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../lessons/skills-table.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
  </div><!-- container -->
  <script src="<?= mix('js/notify.min.js'); ?>"></script>
  <script src="<?= mix('js/lessonToggleTutorialPage.js'); ?>"></script>
</body>
</html>
