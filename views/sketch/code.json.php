{
  "status":"<?= $this->esc($status); ?>",
  "message":"<?= $this->esc($message); ?>",
<?php 
  $fileJson = json_encode($sketchFiles);
?>
  "files": <?= $fileJson ?>
}
