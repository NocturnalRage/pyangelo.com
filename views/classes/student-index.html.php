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
          <h1>Classes I am Taking</h1>
        </div>
        <?php
          if (empty($classes)) {
            include 'no-student-classes.html.php';
          }
          else {
            include 'student-classes.html.php';
          }
        ?>
      <div><!-- col-md-9 -->
    <div><!-- row -->
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
    ?>
  </div><!-- container -->
</body>
</html>
