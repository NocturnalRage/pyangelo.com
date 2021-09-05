<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <?php include __DIR__ . '/../layout/flash.html.php'; ?>
    <div class="row">
      <?php include __DIR__ . '/../profile/profile-menu.html.php'; ?>
      <div class="col-md-9">
        <div class="text-center add-bottom">
          <h1>Classes I Teach</h1>
          <a href="/classes/teacher/new" class="btn btn-lg btn-primary">
              Create a New Class
          </a>
        </div>
        <?php
          if (empty($classes)) {
            include 'no-classes.html.php';
          }
          else {
            include 'classes.html.php';
          }
        ?>
        <?php if (! empty($archivedClasses)) : ?>
          <h3>Archived Classes</h3>
          <?php
            include 'archived-classes.html.php';
          ?>
        <?php endif; ?>
      <div><!-- col-md-9 -->
    <div><!-- row -->
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
    ?>
  </div><!-- container -->
</body>
</html>
