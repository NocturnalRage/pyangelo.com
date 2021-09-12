<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <!-- Ace Editor
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/mode-python.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ext-language_tools.min.js"></script>

  <!-- Skulpt Files
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <script src="<?= mix('js/skulpt.min.js'); ?>"></script>
  <script src="<?= mix('js/skulpt-stdlib.js'); ?>"></script>

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
            <i class="fa fa-square" aria-hidden="true"></i> <strong>Snake</strong></a>
        </button>
      </div><!-- col-md-3 -->
      <div class="col-md-3">
        <button id="breakoutBtn" class="btn btn-block btn-success">
            <i class="fa fa-building-o" aria-hidden="true"></i> <strong>Breakout</strong></a>
        </button>
      </div><!-- col-md-3 -->
      <div class="col-md-3">
        <button id="randomCirclesBtn" class="btn btn-block btn-warning">
            <i class="fa fa-circle" aria-hidden="true"></i> <strong>Random Circles</strong></a>
        </button>
      </div><!-- col-md-3 -->
      <div class="col-md-3">
        <button id="blankEditorBtn" class="btn btn-block btn-danger">
            <i class="fa fa-eraser" aria-hidden="true"></i> <strong>Write Your Own</strong></a>
        </button>
      </div><!-- col-md-3 -->
    </div><!-- row -->
    <div id="editorWrapper" class="row">
      <div class="col-md-12">
        <div id="editor"></div>
      </div><!-- col-md-12 -->
    </div><!-- row -->

<?php
include 'sketch-console.html.php';
include 'sketch-output.html.php';
include 'sketch-buttons.html.php';
?>

  </div><!-- container -->

<script src="<?= mix('js/playground.js'); ?>"></script>

<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
</body>
</html>
