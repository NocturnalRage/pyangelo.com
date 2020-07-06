<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <?php
        include __DIR__ . DIRECTORY_SEPARATOR . '/profile-menu.html.php';
      ?>
      <div class="col-md-9">
        <h1>Change Your Password</h1>
        <?php
          include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php';
        ?>
        <form method="post" action="/password-validate" class="form-horizontal">
          <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken'] ?>" />
          <div class="<?= isset($errors['loginPassword']) ? ' has-error' : ''; ?>">
            <label for="loginPassword" class="control-label">New password:</label>
              <input type="password" name="loginPassword" id="loginPassword" class="form-control" placeholder="New password" value="<?php if (isset($formVars['loginPassword'])) echo $this->esc($formVars['loginPassword']); ?>" maxlength="30" required autofocus />
              <?php if (isset($errors['loginPassword'])) :?>
                <div class="alert alert-danger"><?= $this->esc($errors['loginPassword']); ?></div>
              <?php endif; ?>
          </div>
          <div class="add-bottom">
            <label class="control-label">
              <input type="checkbox" onchange="togglePassword(this)" />
              Show password
            </label>
          </div>
          <script>
          function togglePassword(val) {
            $('#loginPassword').attr('type', val.checked ? 'text' : 'password');
          }
          </script>

          <div>
            <input type="submit" class="btn btn-info" value="Update My Password" />
          </div>
        </form>
      </div><!-- col-md-9 -->
    </div><!-- row -->

<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
