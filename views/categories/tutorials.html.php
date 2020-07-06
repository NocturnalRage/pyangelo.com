    <?php foreach (array_chunk($tutorials, 4, true) as $tutorialgrouping) : ?>
      <?php $tutorialcount = 0; ?>
      <div class="row">
        <?php foreach($tutorialgrouping as $tutorial) : ?>
          <?php $tutorialcount++; ?>
          <div class="col-xs-12 col-sm-6 col-md-3 add-bottom">
            <a class="tutorials-link" href="/tutorials/<?= $this->esc($tutorial['slug']); ?>">
              <img src="/uploads/images/tutorials/<?= $this->esc($tutorial['thumbnail']); ?>" 
                   alt="<?= $this->esc($tutorial['title']); ?>" class="img-responsive featuredThumbnail"
              >
              <h4><span class="label label-<?= $this->esc(strtolower($tutorial['level'])); ?>"><?= $this->esc($tutorial['level']); ?></span></h4>
              <h3><?= $this->esc($tutorial['title']); ?></h3>
            </a>
          </div>
          <?php if ($tutorialcount == 2) : ?>
            <div class="clearfix visible-sm"></div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div><!-- row -->
    <?php endforeach; ?>
