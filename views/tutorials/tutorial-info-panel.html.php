    <div class="panel panel-default">
      <div class="panel-body tutorial-info">
        <a href="/categories/<?= $this->esc($tutorial['category_slug']) ?>" class="pull-left">
          <i class="fa fa-long-arrow-left" aria-hidden="true"></i> Back to <?= $this->esc($tutorial['category']) ?>
        </a>
        <span class="pull-right info-span">
          <strong id="percent-complete"><?= $this->esc($tutorial['percent_complete']); ?></strong>% COMPLETE
        </span>
        <span class="pull-right info-span">
          <i class="fa fa-video-camera" aria-hidden="true"></i> <strong><?= $this->esc($tutorial['lesson_count']); ?></strong> LESSON<?= $tutorial['lesson_count'] != 1 ? 'S' : '' ?>
        </span>
      </div>
    </div>
