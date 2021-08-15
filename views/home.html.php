<?php
include __DIR__ . DIRECTORY_SEPARATOR . 'layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . 'layout/navbar.html.php';
?>
  <div class="container">
    <?php include __DIR__ . '/layout/flash.html.php'; ?>
    <div class="jumbotron">
      <div class="row">
        <div class="col-md-12 add-bottom">
          <p>
          Welcome to PyAngelo, where you can learn to program using Python online. We have extensive tutorials for people of all skill levels developed by registered teachers so you learn the important concepts of programming whilst having fun. Sign up now for free and start coding.
          </p>
          <a href="/register" class="btn btn-lg btn-primary">
            <i class="fa fa-user-plus" aria-hidden="true"></i> <strong>Create Your Free Account</strong></a>
        </div>
      </div><!-- row -->
    </div>
    <div class="row">
      <div class="col-md-12 add-bottom text-center">
        <h2>Examples</h2>
        <p>
        Want to see how to code a game of snake or breakout? Or how to draw random circles on the screen? We've got some example programs that you can run and modify, or just write your own program without an account.
        </p>
        <p>
          <a href="/playground" class="btn btn-lg btn-success">
            <i class="fa fa-code-o" aria-hidden="true"></i> <strong>Start Coding</strong></a>
        </p>
      </div>
    </div>
    <?php include __DIR__ . '/layout/footer.html.php'; ?>
  </div><!-- container -->
</body>
</html>
