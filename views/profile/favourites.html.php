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
        <h1>My Favourites</h1>
        <?php
          foreach ($favourites as $favourite) {
            include __DIR__ . DIRECTORY_SEPARATOR . '/favourite.html.php';
          }
        ?>    
      </div><!-- col-md-9 -->
    </div><!-- row -->
<?php
include __DIR__ . '/../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
