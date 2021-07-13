<?php foreach($sketches as $sketch) : ?>
  <h3><a href="/sketch/<?= $this->esc($sketch['sketch_id']) ?>"><?= $this->esc($sketch['title']) ?></a></h3>
  <?php
    $lastUpdatedDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sketch['updated_at'])->diffForHumans();
  ?>
  <p>Last saved: <em><?= $this->esc($lastUpdatedDate) ?></em></p>
  <hr />
<?php endforeach; ?>
