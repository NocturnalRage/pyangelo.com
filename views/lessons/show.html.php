<?php
  include __DIR__ . '/../layout/header.html.php';
  include __DIR__ . '/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div id="video-container" class="col-md-8 col-md-offset-2">
        <?php if ($lesson['youtube_url']) : ?>
          <div id="pyangelo-lesson"
               class="embed-responsive embed-responsive-16by9"
               data-lesson-id="<?= $this->esc($lesson['lesson_id']); ?>"
               data-tutorial-id="<?= $this->esc($lesson['tutorial_id']); ?>"
               data-display-order="<?= $this->esc($lesson['display_order']); ?>"
          >
            <iframe id="pyangelo-video"
                    class="embed-responsive-item"
                    src="https://www.youtube.com/embed/<?= $this->esc($lesson['youtube_url']) ?>?enablejsapi=1&rel=0&modestbranding=1&showinfo=0"
                    allowfullscreen>
            </iframe>
          </div>
        <?php else: ?>
          <video id="pyangelo-lesson"
                 data-lesson-id="<?= $this->esc($lesson['lesson_id']); ?>"
                 data-tutorial-id="<?= $this->esc($lesson['tutorial_id']); ?>"
                 data-display-order="<?= $this->esc($lesson['display_order']); ?>"
                 class="video-js vjs-big-play-centered vjs-16-9"
                 controls
                 preload="metadata"
                 <?php if (isset($lesson['poster'])) : ?>
                   poster="<?= $this->esc('/uploads/images/lessons/' . $lesson['poster']) ?>"
                 <?php endif; ?>
          >
            <?php foreach ($captions as $caption) : ?>
            <track kind="captions" src="/uploads/captions/lessons/<?= $this->esc($caption['caption_filename']); ?>" srclang="<?= $this->esc($caption['srclang']); ?>" label="<?= $this->esc($caption['language']);?>"<?= $caption['srclang'] == 'en' ? ' default' : '' ?> />
            <?php endforeach; ?>
            <p class="vjs-no-js">
              To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>
          </video>
        <?php endif; ?>
        <div id="whatNextPanel" class="panel panel-default">
          <div class="panel-body">
            <h3 class="text-center">What next?</h3>
            <a id="nextLessonButton" href="#" class="btn btn-primary btn-block">Next lesson</a>
            <?php if ($personInfo['loggedIn']) : ?>
              <a href="#comment-anchor" class="btn btn-primary btn-block"><i class="fa fa-comments-o" aria-hidden="true"></i> Discuss this lesson</a>
            <?php endif; ?>
            <a id="replayVideo" href="#" class="btn btn-success btn-block"><i class="fa fa-repeat" aria-hidden="true"></i> Replay Video</a>
          </div>
        </div>
        <div id="loadingVideoPanel" class="panel panel-default">
          <div class="panel-body">
            <h3 class="text-center">Loading Video</h3>
            <img src="/images/icons/ajax-loader.gif" alt="Loading ..." class="center-block" />
          </div>
        </div>
      </div><!-- video-container -->
    </div><!-- row -->
    <br />
    <?php if ($personInfo['isAdmin']) : ?>
      <div class="row">
        <div class="col-md-8 col-md-offset-2 text-center">
        <a href="/tutorials/<?= $this->esc($lesson['tutorial_slug']); ?>/lessons/<?= $this->esc($lesson['lesson_slug']); ?>/edit" class="btn btn-warning">
          <i class="fa fa-pencil-square-o"></i> Edit Lesson</a>
        <a href="/captions/<?= $this->esc($lesson['tutorial_slug']) ?>/<?= $this->esc($lesson['lesson_slug']); ?>" class="btn btn-info">
          <i class="fa fa-cc"></i> Lesson Captions</a>
        </div>
      </div><!-- row -->
      <br />
    <?php endif; ?>

    <?php if ($personInfo['loggedIn']) : ?>
      <?php
        include __DIR__ . '/../sketch/sketch-editor.html.php';
        include __DIR__ . '/../sketch/sketch-console.html.php';
        include __DIR__ . '/../sketch/sketch-buttons.html.php';
        include __DIR__ . '/../sketch/sketch-output.html.php';
        include __DIR__ . '/../sketch/sketch-upload.html.php';
      ?>

      <script src="<?= mix('js/PyAngeloSetup.js'); ?>"></script>
      <script src="<?= mix('js/editor.js'); ?>"></script>
      <script>
      loadCode();
      </script>

    <?php else : ?>
      <div class="row">
        <div class="col-md-12">
          <h1 class="text-center">Write Code While Watching This Lesson</h1>
          <p class="text-center"><a href="/Login">Login</a> or <a href="/register">create an account</a> and code along whilst you watch this lesson.</p>
        </div>
      </div>
    <?php endif; ?>

    <div class="row">
      <div class="col-md-9">
        <div class="panel panel-default">
          <div class="panel-body">
            <h1 class="text-center"><?= $this->esc($lesson['lesson_title']); ?></h1>
            <p class="text-center"><?= $this->esc($lesson['lesson_description']); ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="panel panel-default">
          <div class="panel-body">
            <a id="completeStatus" class="toggleComplete btn btn-block <?= $lesson['completed'] ? 'btn-success' : 'btn-default' ?> btn<?= $this->esc($lesson['lesson_id']); ?>" href="#" data-lesson-id="<?= $this->esc($lesson['lesson_id']); ?>" aria-label="Toggle completion">
              <i class="fa fa-check" aria-hidden="true"></i>
              <?= $lesson['completed'] ? 'Complete' : 'Incomplete' ?>
            </a>
            <a id="favouriteStatus" class="btn btn-block <?= $lesson['favourited'] ? 'btn-primary' : 'btn-default' ?>" href="#" data-lesson-id="<?= $this->esc($lesson['lesson_id']); ?>" aria-label="Toggle favourited">
              <i class="fa fa-star" aria-hidden="true"></i>
              <?= $lesson['favourited'] ? 'Favourite' : 'Add to Favourites' ?>
            </a>
          </div>
        </div>
      </div>
    </div><!-- row -->
    <hr />
    <div class="row">
      <div class="col-md-9">
        <h4 class="text-center">Tutorial Lessons</h4>
        <h2 class="text-center"><?= $lesson['tutorial_title']; ?></h2>
        <?php if (! empty($tutorial['pdf'])) : ?>
          <div class="tutorial-pdf text-center add-bottom">
            <a href="/uploads/pdf/tutorials/<?= $this->esc($tutorial['pdf']); ?>" target="_blank" class="btn btn-lg btn-primary">
              <i class="fa fa-file-pdf-o"></i> <strong>Download Tutorial PDF</strong></a>
          </div>
        <?php endif; ?>
      </div>
      <div class="col-md-3">
        <img src="/uploads/images/tutorials/<?= $this->esc($lesson['tutorial_thumbnail']); ?>"
             alt="<?= $this->esc($lesson['tutorial_title']); ?>" class="img-responsive featuredThumbnail" />
      </div>
    </div>
    <?php
      include __DIR__ . '/../tutorials/tutorial-info-panel.html.php';
      include __DIR__ . '/lessons-table.html.php';
      include __DIR__ . '/lesson-comments.html.php';
      include __DIR__ . '/../layout/footer.html.php';
    ?>
  </div><!-- container -->
  <?php if (! $lesson['youtube_url']) : ?>
    <script src="//vjs.zencdn.net/5.8.8/video.js"></script>
    <script src="//cdn.sc.gl/videojs-hotkeys/0.2/videojs.hotkeys.min.js"></script>
  <?php endif; ?>
  <script src="<?= mix('js/notify.min.js'); ?>"></script>
  <script src="<?= mix('js/lessonToggle.js'); ?>"></script>
  <script src="<?= mix('js/lessonComments.js'); ?>"></script>
  <script src="https://cdn.tiny.cloud/1/1fnyfp2h3lz7wu4v2qlchoping5fjj7mdbonk8ekgaczk7vh/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
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
