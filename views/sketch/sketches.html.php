<?php foreach($sketches as $sketch) : ?>
  <h3><a href="/sketch/<?= $this->esc($sketch['sketch_id']) ?>"><?= $this->esc($sketch['title']) ?></a></h3>
  <hr />
<?php endforeach; ?>
