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
</script>

<script>
Sk.PyAngelo.console.style.display = "none";
Sk.PyAngelo.console.style.display = "none";
pyEditor = document.getElementById('editor');
pyEditor.style.display = "none";
pyEditorFiles = document.getElementById('editorFiles');
pyEditorFiles.style.display = "none";
forkParagraph = document.getElementById('forkParagraph');
if (forkParagraph)
    forkParagraph.style.display = "none";
</script>

</body>
</html>
