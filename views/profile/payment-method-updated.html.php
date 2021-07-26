<?php
include __DIR__ . '/../layout/header.html.php';
include __DIR__ . '/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <?php
        include __DIR__ . DIRECTORY_SEPARATOR . '/profile-menu.html.php';
      ?>
      <div class="col-md-9">
        <h1>Payment Method Updated</h1>
        <p>Your payment method was successfully updated.</p>
      </div><!-- col-md-9 -->
    </div><!-- row -->
<?php
include __DIR__ . '/../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
