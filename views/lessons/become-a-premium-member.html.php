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
        <a href="/premium-membership">
          <img src="/uploads/images/tutorials/<?= $this->esc($lesson['tutorial_thumbnail']); ?>" class="img-responsive center-block featuredThumbnail">
        </a>
        <p>
        To watch this video you need to be a premium member. By becoming a
        premium member you'll get online access to every PyAngelo video
        we've ever made and learn the coding strategies taught by our teachers. 
        </p>
        <p>
          <a href="/premium-membership" class="btn btn-large btn-primary btn-responsive">
          <i class="fa fa-cube"></i> FIND OUT MORE ABOUT THE PREMIUM MEMBERSHIP</a>
        </p>
      </div><!-- col-md-8 -->
    </div><!-- row -->

<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
