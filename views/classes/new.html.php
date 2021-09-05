<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <?php include __DIR__ . '/../profile/profile-menu.html.php'; ?>
      <div class="col-md-9">
        <div class="text-center">
  
          <h1 class="text-center">Create a New Class</h1>
          <hr />
          <?php
            include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php';
          ?>
            <form id="classForm"
              method="post"
              action="/classes/teacher/create"
              enctype="multipart/form-data"
            >
            <?php
              include __DIR__ . DIRECTORY_SEPARATOR . 'form.html.php';
            ?>
      </div><!-- col-md-9 -->
    </div><!-- row -->
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
    ?>
  </div><!-- container -->
</body>
</html>
