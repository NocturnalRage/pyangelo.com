<?php
include __DIR__ . '/../layout/header.html.php';
include __DIR__ . '/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <?php
        include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php';
        include __DIR__ . DIRECTORY_SEPARATOR . '/profile-menu.html.php';
      ?>
      <div class="col-md-9">
        <div class="media-left">
          <img class="media-object featuredThumbnail" src="<?= $avatar->getAvatarUrl($person['email']) ?>" alt="<?= $this->esc($person['given_name'] . ' ' . $person['family_name']) ?>" />
        </div>
        <div class="media-body">
        <h1 class="media-heading"><?= $this->esc($person['given_name']) ?> <?= $this->esc($person['family_name']) ?></h1>
          <p><?= $this->esc($person['email']) ?></p>
          <p>Joined PyAngelo <?= $this->esc($person['memberSince']) ?></p>
          <p><?= $this->esc($person['country_name']) ?></p>
        </div>
        <div>
          <a href="/profile/edit" class="btn btn-success">
            <i class="fa fa-pencil-square-o"></i> Edit Profile</a>
        </div>
      </div><!-- col-md-9 -->
    </div><!-- row -->
    <?php
      include __DIR__ . '/../layout/footer.html.php';
    ?>
  </div><!-- container -->
</body>
</html>
