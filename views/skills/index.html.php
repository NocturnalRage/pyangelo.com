<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <?php if ($personInfo['isAdmin']) : ?>
      <a href="/skills/new" class="btn btn-warning">
        <i class="fa fa-plus"></i> New Skill</a>
      <hr />
    <?php endif; ?>
    <?php foreach($skills as $skill) : ?>
      <h2><?= $skill['skill_name'] ?></h2>
    <?php endforeach; ?>

    <?php
      include __DIR__ . '/../layout/footer.html.php';
    ?>
  </div><!-- container -->
</body>
</html>
