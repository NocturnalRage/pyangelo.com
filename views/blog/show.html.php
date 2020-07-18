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
  <script src="<?= mix('js/notify.min.js'); ?>"></script>
  <script src="<?= mix('js/blogAlert.js'); ?>"></script>
  <script src="<?= mix('js/blogComments.js'); ?>"></script>
  <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
  <script type="text/javascript">
tinymce.init({
  selector: 'textarea.tinymce',
  toolbar_items_size: 'small',
  plugins: "link, image, hr, lists",
  relative_urls : false,
  browser_spellcheck: true,
  toolbar: "undo redo | formats formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink image media | hr blockquote",
  image_class_list : [ {title: 'Responsive Image', value: 'img-responsive' } ],
  image_caption: true,
  menubar: false,
  statusbar: false
});
  </script>
</body>
</html>
