<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <?php
        include __DIR__ . DIRECTORY_SEPARATOR . '/../layout/flash.html.php';
        include __DIR__ . DIRECTORY_SEPARATOR . '/admin-menu.html.php';
      ?>
      <div class="col-md-9">
        <h1>Search for a User</h1>
        <form id="searchForm" method="get" action="#">
          <div id="searchDiv" class="form-group">
            <input type="text" name="search" id="search" class="form-control" placeholder="Search by name or email address..." maxlength="100" required autofocus />
          </div>

        </form>
        <div id="search-results">
        </div>
      </div><!-- col-md-9 -->
    </div><!-- row -->
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
    ?>
  </div><!-- container -->
  <script src="<?= mix('js/userSearch.js'); ?>"></script>
</body>
</html>
