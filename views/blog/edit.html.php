<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <h1 class="text-center">Edit <?= $this->esc($blog['title']); ?> Blog</h1>
    <hr />
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php';
    ?>
    <div class="row">
      <div class="col-md-12">
        <form id="blogForm"
              method="post"
              action="/blog/<?= $this->esc($blog['slug']); ?>/update"
              enctype="multipart/form-data"
        >
        <?php
          include __DIR__ . DIRECTORY_SEPARATOR . 'form.html.php';
        ?>
      </div><!-- col-md-12 -->
    </div><!-- row -->
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . 'video-instructions.html.php';
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
    ?>
  </div><!-- container -->
  <script src="<?= mix('js/tinymce-default-config.js'); ?>"></script>
</body>
</html>
