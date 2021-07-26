<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <?php
        include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php';
        include __DIR__ . DIRECTORY_SEPARATOR . '/admin-menu.html.php';
      ?>
      <div class="col-md-9">
        <h1>PyAngelo Metrics</h1>
        <?php
          include __DIR__ . DIRECTORY_SEPARATOR . '/count-metrics.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . '/subscriber-growth.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . '/subscriber-payments.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . '/premium-member-count.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . '/premium-member-countries.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . '/free-members-per-month.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . '/free-members-per-day.html.php';
          include __DIR__ . DIRECTORY_SEPARATOR . '/free-member-countries.html.php';
        ?>
      </div><!-- col-md-9 -->
    </div><!-- row -->
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
    ?>
  </div><!-- container -->
</body>
</html>
