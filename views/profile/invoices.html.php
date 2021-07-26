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
        <?php if (empty($payments)) : ?>
          <h1>You Haven't Made Any Payments</h1>
          <p>If you <a href="/premium-membership">sign up</a> to our monthly plan, you'll see a record of any payments you make to PyAngelo right here on this page.</p>
        <?php else : ?>
          <?php         include __DIR__ . '/payment-history.html.php'; ?>
        <?php endif ?>
      </div><!-- col-md-9 -->
    </div><!-- row -->
<?php
include __DIR__ . '/../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
