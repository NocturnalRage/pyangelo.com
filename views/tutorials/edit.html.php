<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <h1 class="text-center">Edit <?= $this->esc($tutorial['title']); ?> Tutorial</h1>
    <hr />
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php';
    ?>
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
      <form id="tutorialForm"
            method="post"
            action="/tutorials/<?= $this->esc($tutorial['slug']); ?>/update"
            enctype="multipart/form-data">
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
