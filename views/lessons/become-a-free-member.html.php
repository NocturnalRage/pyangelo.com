<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <h1 class="text-center"><?= $this->esc($lesson['lesson_title']); ?></h1>
    <h2 class="text-center"><?= $this->esc($lesson['tutorial_title']); ?></h2>
    <hr />
    <div class="row">
      <div class="col-md-8 col-md-offset-2 text-center">
        <a href="/register">
          <img src="/uploads/images/tutorials/<?= $this->esc($lesson['tutorial_thumbnail']); ?>" class="img-responsive center-block featuredThumbnail">
        </a>
        <p>
        To watch this video you need to be a free member. It takes 30 seconds
        to create your account and then you'll be able to watch all of our
        free videos and learn to code as instructed by our teachers.
        </p>
        <p>
          <a href="/register" class="btn btn-large btn-primary">
          <i class="fa fa-cube"></i> CREATE YOUR FREE ACCOUNT</a>
        </p>
        <p>Already have an account? <a href="/login">Login</a> now.</p>
      </div><!-- col-md-8 -->
    </div><!-- row -->

<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
