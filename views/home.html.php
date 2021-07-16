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
          Welcome to PyAngelo, where you can learn to program using Python online. We have extensive tutorials for people of all skill levels developed by registered teachers so you learn the important concepts of programming whilst having fun. Sign up now for free and start programming.
          </p>
          <a href="/register" class="btn btn-lg btn-primary">
            <i class="fa fa-user-plus" aria-hidden="true"></i> <strong>Create Your Free Account</strong></a>
        </div>
      </div><!-- row -->
    </div>
    <div class="row">
      <div class="col-md-12 add-bottom text-center">
        <h2>Snake</h2>
        <p>
        As an example of what is possible, we've recreated the classic game of Snake. It was coded in the browser using Python code. What is the highest score you can get?
        </p>
        <p>
          <iframe src="https://www.pyangelo.com/canvasonly/2116" width="630" height="630"></iframe>
        </p>
      </div>
    </div>
    <?php include __DIR__ . '/layout/footer.html.php'; ?>
  </div><!-- container -->
</body>
</html>
