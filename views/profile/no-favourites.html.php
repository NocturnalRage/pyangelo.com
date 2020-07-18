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
        <p>
          You don't have any favourite lessons yet. When you are watching a video you can click the "Add to Favourites" button and then we'll list all of your favourites here so you can find them easily. Watch some <a href="/tutorials">tutorials</a> now to find your first favourite.
        </p>
      </div><!-- col-md-9 -->
    </div><!-- row -->
<?php
include __DIR__ . '/../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
