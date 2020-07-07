<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
  
    <h1 class="text-center">Edit Lesson</h1>
    <h2 class="text-center"><?= $this->esc($lesson['lesson_title']); ?></h2>
    <hr />
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php';
    ?>
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <form id="tutorialForm"
              method="post"
              action="/tutorials/<?= $this->esc($lesson['tutorial_slug']); ?>/lessons/<?= $this->esc($lesson['lesson_slug']); ?>/update"
              enctype="multipart/form-data"
        >
        <?php
          include __DIR__ . DIRECTORY_SEPARATOR . 'form.html.php';
        ?>
      </div><!-- col-md-8 -->
    </div><!-- row -->

<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
