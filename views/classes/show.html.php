<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <?php include __DIR__ . '/../profile/profile-menu.html.php'; ?>
      <div class="col-md-9">
        <?php include __DIR__ . '/../layout/flash.html.php'; ?>
        <div class="text-center add-bottom">
          <h1><?= $this->esc($class['class_name']) ?></h1>
          <?php
            $createdAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $class['created_at'])->diffForHumans();
          ?>
          <p class="text-center"><em>Created <?= $this->esc($createdAt) ?></em></p>
          <p class="text-center">Class Code:  <?= $this->esc($class['class_code']) ?></p>
          <p class="text-center">Join Link:  https://www.pyangelo.com/classes/join/<?= $this->esc($class['class_code']) ?></p>
          <a href="/classes/teacher/<?= $this->esc($class['class_id']) ?>/edit" class="btn btn-warning">
            <i class="fa fa-pencil-square-o"></i> Edit Class</a>
          <a href="/classes/teacher" class="btn btn-info">
            <i class="fa fa-university"></i> Back to All Classes</a>
        </div><!-- text-center -->
        <?php
          if (empty($students)) {
            include 'no-students.html.php';
          }
          else {
            include 'students.html.php';
          }
        ?>
      </div><!-- col-md-9 -->
    </div><!-- row -->
  <?php include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php'; ?>
  </div><!-- container -->
</body>
</html>
