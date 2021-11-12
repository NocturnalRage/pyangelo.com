<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <?php if ($personInfo['isAdmin']) : ?>
      <a href="/tutorials/new" class="btn btn-warning">
        <i class="fa fa-plus"></i> New Tutorial</a>
      <a href="/skills" class="btn btn-info">
        <i class="fa fa-university"></i> Skills</a>
      <hr />
    <?php endif; ?>
    <?php
      $groups=array();
      foreach ($tutorials as $tutorial) {
        $groups[$tutorial['category_slug']][] = $tutorial;
      }
    ?>
    <?php foreach($groups as $tutorials) : ?>
      <h1><a href="/categories/<?= $this->esc($tutorials[0]['category_slug']) ?>"><?= $this->esc($tutorials[0]['category']) ?></a></h1>
      <?php
        include __DIR__ . '/../categories/tutorials.html.php';
      ?>
      <hr />
    <?php endforeach; ?>

    <?php
      include __DIR__ . '/../layout/footer.html.php';
    ?>
  </div><!-- container -->
</body>
</html>
