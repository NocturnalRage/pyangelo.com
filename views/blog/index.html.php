<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <?php if ($personInfo['isAdmin']) : ?>
      <a href="/blog/new" class="btn btn-warning">
        <i class="fa fa-plus"></i> New Blog</a>
      <br />
      <br />
    <?php endif; ?>
    <?php
      if (! empty($featuredBlogs)) {
        $displayBlogs = $featuredBlogs;
        $blogClass = " well well-lg";
        $blogTypeTitle = 'Featured Blogs';
        include __DIR__ . DIRECTORY_SEPARATOR . 'blog-previews.html.php';
      }
      $displayBlogs = $blogs;
      $blogClass = 'regular-blogs';
      $blogTypeTitle = '';
      include __DIR__ . DIRECTORY_SEPARATOR . 'blog-previews.html.php';
    ?>
<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
