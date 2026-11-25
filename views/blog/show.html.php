<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <?php if ($personInfo['isAdmin']) : ?>
      <a href="/blog/<?= $this->esc($blog['slug']) ?>/edit" class="btn btn-warning">
        <i class="fa fa-pencil-square-o"></i> Edit Blog</a>
      <hr />
    <?php endif; ?>
    <h1 class="text-center"><?= $this->esc($blog['title']) ?></h1>
    <?php
      $publishedDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $blog['published_at'])->diffForHumans();
    ?>
    <p class="text-center"><em><?= $this->esc($publishedDate) ?></em></p>
    <h4><span class="label label-<?= $this->esc(str_replace(' ', '-', strtolower($blog['category_description']))); ?>"><?= $this->esc($blog['category_description']); ?></span></h4>
    <div><?= $purifier->purify($blog['content']); ?></div>
    <?php include __DIR__ . '/blog-comments.html.php'; ?>
    <?php include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php'; ?>
  </div><!-- container -->
  <?= viteScript('resources/assets/js/notify.js'); ?>
  <?= viteScript('resources/assets/js/blogAlert.js'); ?>
  <?= viteScript('resources/assets/js/blogComments.js'); ?>
  <?= viteScript('resources/assets/js/tinymce-comments.js'); ?>
</body>
</html>
