<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
?>
  <div class="container">

<?php
include 'sketch-editor.html.php';
include 'sketch-console.html.php';
include 'sketch-output.html.php';
include 'sketch-buttons.html.php';
?>

  </div><!-- container -->

<script src="<?= mix('js/PyAngeloSetup.js'); ?>"></script>
<script src="<?= mix('js/editor.js'); ?>"></script>
<script>
loadCodeAndRun();
consoleWrapper = document.getElementById('consoleWrapper');
consoleWrapper.style.display = "none";
editorWrapper = document.getElementById('editorWrapper');
editorWrapper.style.display = "none";
pyEditorFiles = document.getElementById('editorFiles');
pyEditorFiles.style.display = "none";
buttonsWrapper = document.getElementById('buttonsWrapper');
buttonsWrapper.style.display = "none";
</script>

</body>
</html>
