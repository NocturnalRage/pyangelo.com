<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1 class="text-center">PyAngelo Playground</h1>
        <p>You can experiment in the playground by writing Python code without needing to create an account. However you will not be able to save your work or upload sounds or images. Hence we encourage you to <a href="/register">create a free account</a> which will also give you access to many free tutorials.</p>
        <p>Press the buttons to load different programs. Scroll down and click the start button to run it. Then try and modify the program. For example, in the Snake game, can you modify the code so you score more points? If you wish to write your own program from scratch, just delete the code and start writing. Enjoy!</p>
      </div><!-- col-md-12 -->
    </div><!-- row -->
    <div class="row add-bottom text-center">
      <div class="col-md-3">
        <button id="snakeBtn" class="btn btn-block btn-primary">
            <i class="fa fa-square" aria-hidden="true"></i> <strong>Snake</strong>
        </button>
      </div><!-- col-md-3 -->
      <div class="col-md-3">
        <button id="breakoutBtn" class="btn btn-block btn-success">
            <i class="fa fa-building-o" aria-hidden="true"></i> <strong>Breakout</strong>
        </button>
      </div><!-- col-md-3 -->
      <div class="col-md-3">
        <button id="randomCirclesBtn" class="btn btn-block btn-warning">
            <i class="fa fa-circle" aria-hidden="true"></i> <strong>Random Circles</strong>
        </button>
      </div><!-- col-md-3 -->
      <div class="col-md-3">
        <button id="blankEditorBtn" class="btn btn-block btn-danger">
            <i class="fa fa-eraser" aria-hidden="true"></i> <strong>Write Your Own</strong>
        </button>
      </div><!-- col-md-3 -->
    </div><!-- row -->
  </div><!-- container -->
  <div class="container-fluid">
    <?php
      include 'sketch-split-editor-console.html.php';
      include 'sketch-debug-table.html.php';
      include 'sketch-output.html.php';
      include 'sketch-turtle.html.php';
      include 'sketch-buttons.html.php';
    ?>
    <script src="<?= mix('js/playground.js'); ?>"></script>
  </div><!-- container-fluid -->

  <div class="container">
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
    ?>
  </div><!-- container -->
</body>
</html>
