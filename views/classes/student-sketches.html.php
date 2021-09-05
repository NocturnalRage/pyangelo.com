<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <?php include __DIR__ . '/../profile/profile-menu.html.php'; ?>
      <div class="col-md-9">
        <div class="text-center add-bottom">
          <h1><?= $this->esc($student['given_name']) ?> <?= $this->esc($student['family_name']) ?></h1>
          <h3><?= $this->esc($class['class_name']) ?></h3>
          <?php
            $joinedAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $student['joined_at'])->diffForHumans();
          ?>
          <p class="text-center"><em>Joined <?= $this->esc($joinedAt) ?></em></p>
        </div><!-- text-center -->
        <?php
          if (empty($sketches)) {
            include 'no-sketches.html.php';
          }
          else {
            include 'sketches.html.php';
          }
        ?>
      </div><!-- col-md-9 -->
    </div><!-- row -->
  <?php include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php'; ?>
  </div><!-- container -->
</body>
</html>
