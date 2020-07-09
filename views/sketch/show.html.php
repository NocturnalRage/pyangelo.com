<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">

<?php
include 'sketch-title.html.php';
include 'sketch-editor.html.php';
include 'sketch-console.html.php';
include 'sketch-buttons.html.php';
include 'sketch-output.html.php';
include 'sketch-upload.html.php';
?>

  </div><!-- container -->

<script src="<?= mix('js/editor.js'); ?>"></script>

<script>
    function writeOutput(data, append) {
        if (append) document.getElementById("console").innerHTML += data;
        else document.getElementById("console").innerHTML = data;
		
		// keep scrolled to the bottom
		document.getElementById("console").scrollTop = document.getElementById("console").scrollHeight
    }
</script>

<script type="text/python3" id="sketchEditor">
    import pyangelo
</script>

<div class="row">
  <div class="col-md-12">
  <br />
  <p class="text-center">
    View your sketch in <a href="/run/<?= $this->esc($sketch['sketch_id']) ?>">Run Mode</a>
  </p>
  <p class="text-center">
    View your sketch in <a href="/present/<?= $this->esc($sketch['sketch_id']) ?>">Presentation Mode</a>
  </p>
  </div><!-- col-md-12 -->
</div><!-- row -->
<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>

</body>
</html>
