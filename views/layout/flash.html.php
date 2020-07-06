<?php if (! empty($flash)): ?>
  <div class="alert alert-<?= $this->esc($flash['type']) ?> alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <?= $this->esc($flash['message']); ?>
  </div>
<?php endif; ?>
