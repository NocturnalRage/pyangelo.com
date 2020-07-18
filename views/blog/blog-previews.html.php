    <div class="<?= $blogClass ?>">
      <?php if (! empty($blogTypeTitle)) : ?>
        <h1 class="text-center"><?= $this->esc($blogTypeTitle) ?></h1>
      <?php endif; ?>
      <?php $blogGroupCount = 0; ?>
      <?php $blogCount = 0; ?>
      <?php foreach (array_chunk($displayBlogs, 3, true) as $blogGrouping) : ?>
        <?php $blogGroupCount++; ?>
        <div class="row">
          <?php if ($blogGroupCount > 1) : ?>
            <hr class="hidden-xs"/>
          <?php endif; ?>
          <?php foreach($blogGrouping as $blog) : ?>
            <?php $blogCount++; ?>
            <?php if ($blogCount > 1) : ?>
              <hr class="visible-xs"/>
            <?php endif; ?>
            <div class="col-sm-4">
              <a class="blog-link" href="/blog/<?= $this->esc($blog['slug']); ?>">
                <?php
                  $publishedDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $blog['published_at'])->diffForHumans();
                ?>
                <img src="/uploads/images/blog_thumbnail/<?= $this->esc($blog['blog_image']); ?>"
                     alt="<?= $this->esc($blog['title']); ?>" class="img-responsive featuredThumbnail"
                >
                <h3><?= $this->esc($blog['title']); ?></h3>
                <p><em><?= $this->esc($publishedDate) ?></em></p>
                <h4><span class="label label-<?= $this->esc(str_replace(' ', '-', strtolower($blog['category_description']))); ?>"><?= $this->esc($blog['category_description']); ?></span></h4>
                <div><?= $purifier->purify($blog['preview']); ?></div>
              </a>
            </div>
          <?php endforeach; ?>
        </div><!-- row -->
      <?php endforeach; ?>
    </div>
