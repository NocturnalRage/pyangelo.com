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
        <a href="/choose-plan">
          <img src="/uploads/images/tutorials/<?= $this->esc($lesson['tutorial_thumbnail']); ?>" class="img-responsive center-block featuredThumbnail">
        </a>
        <p>
        To watch this video you need to sign up to one of our monthly
        subscriptions. By subscribing you'll get full access to every
        PyAngelo tutorial and learn important coding techniques.
        </p>
        <p>
          <a href="/choose-plan" class="btn btn-large btn-primary btn-responsive">
          <i class="fa fa-cube"></i> FIND OUT MORE ABOUT OUR MONTHLY SUBSCRIPTIONS</a>
        </p>
      </div><!-- col-md-8 -->
    </div><!-- row -->

<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
