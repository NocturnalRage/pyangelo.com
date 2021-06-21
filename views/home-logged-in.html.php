<?php
include __DIR__ . DIRECTORY_SEPARATOR . 'layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . 'layout/navbar.html.php';
?>
  <div class="container">
    <?php include __DIR__ . '/layout/flash.html.php'; ?>
    <div class="jumbotron">
      <div class="row">
        <div class="col-md-12">
          <p>
          Welcome to back to PyAngelo, where you can learn to program using Python online. We have extensive tutorials for people of all skill levels developed by registered teachers so you learn the important concepts of programming whilst having fun.
          </p>
        </div>
      </div><!-- row -->
    </div><!-- jumbotron -->
    <div class="row add-bottom">
      <div class="col-md-12 text-center">
        <h1>Choose What To Do Next</h1>
        <p>Click a button below to continue your coding journey</p>
      </div>
    </div><!-- row -->
    <div class="row text-center">
      <div class="col-md-4 add-bottom">
          <a href="/sketch/create" class="btn btn-lg btn-primary"
            onclick="event.preventDefault();
            document.getElementById('create-sketch-form').submit();">
            <i class="fa fa-plus" aria-hidden="true"></i> <strong>Create a New Sketch</strong></a>
          <form id="create-sketch-form" action="/sketch/create" method="POST" style="display: none;">
            <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
          </form>
      </div>
      <div class="col-md-4 add-bottom">
          <a href="/sketch" class="btn btn-lg btn-info">
            <i class="fa fa-th-list" aria-hidden="true"></i> <strong>View Your Sketches</strong></a>
      </div>
      <div class="col-md-4 add-bottom">
          <a href="/tutorials" class="btn btn-lg btn-success">
            <i class="fa fa-video-camera" aria-hidden="true"></i> <strong>Watch a Tutorial</strong></a>
      </div>
    </div><!-- row -->
    <?php include __DIR__ . '/layout/footer.html.php'; ?>
  </div><!-- container -->
</body>
</html>
