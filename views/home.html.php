<?php
include __DIR__ . DIRECTORY_SEPARATOR . 'layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . 'layout/navbar.html.php';
?>
  <div class="container">
    <?php include __DIR__ . '/layout/flash.html.php'; ?>
    <div class="jumbotron">
      <div class="row">
        <div class="col-md-6 add-bottom">
          <p>
          Welcome to PyAngelo, where you can learn to program using Python online. We have extensive tutorials for people of all skill levels developed by registered teachers so you learn the important concepts of programming whilst having fun. Sign up now for free and start programming.
          </p>
          <a href="/register" class="btn btn-lg btn-primary">
            <i class="fa fa-user-plus" aria-hidden="true"></i> <strong>Create Your Free Account</strong></a>
        </div>
        <div class="col-md-6 add-bottom">
          <h2>Include example program or video here!</h2>
        </div>
      </div><!-- row -->
    </div>
    <?php include __DIR__ . '/layout/footer.html.php'; ?>
  </div><!-- container -->
</body>
</html>
