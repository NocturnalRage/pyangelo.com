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
        <h1>Premium Members</h1>
        <?php foreach ($premiumMembers as $person) : ?>
          <hr />
          <a href="/admin/users/<?= $this->esc($person['person_id']) ?>">
          <?php
            include __DIR__ . DIRECTORY_SEPARATOR . 'person.html.php';
          ?>
          </a>
        <?php endforeach; ?> 
      </div><!-- col-md-9 -->
    </div><!-- row -->
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
    ?>
  </div><!-- container -->
</body>
</html>
