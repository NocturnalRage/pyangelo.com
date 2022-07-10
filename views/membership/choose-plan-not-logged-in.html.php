<?php
  include __DIR__ . '/../layout/header.html.php';
  include __DIR__ . '/../layout/navbar.html.php';
?>
  <div class="container">
    <?php include __DIR__ . '/../layout/flash.html.php'; ?>
    <div class="row">
      <div class="col-md-12 text-center add-bottom">
        <?php
          include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php';
        ?>
        <h3>Do you already have a free PyAngelo account?</h3>
        <p>
          You need a free account before you can subscribe to one of our
          monthly plans. If you already have one then sign in, otherwise
          create one now, it takes less than 60 seconds.
        </p>
      </div><!-- col-md-12 -->
    </div><!-- row -->
    <div class="row">
      <div class="col-sm-4 col-sm-offset-2 text-center">
        <h5>Yes, I'm already a free member</h5>
        <a href="/login" class="btn btn-primary">
          <i class="fa fa-sign-in" aria-hidden="true"></i> Login To Your Account</a>
      </div>
      <div class="col-sm-4 text-center">
        <h5>No, I'll create my free account now</h5>
        <a href="/register" class="btn btn-success">
          <i class="fa fa-user-plus" aria-hidden="true"></i> Create Your Free Account</a>
      </div>
    </div><!-- row -->

    <?php include __DIR__ . '/../layout/footer.html.php'; ?>
  </div><!-- container -->
</body>
</html>
