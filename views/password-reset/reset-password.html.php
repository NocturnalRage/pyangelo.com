<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
  
    <h1 class="text-center">You're Nearly Done</h1>
    <p class="text-center">
    You can now reset your password by entering a new one below.
    </p>
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php';
    ?>
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <form method="post" action="/reset-password-validate" class="form-horizontal">
          <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
          <input type="hidden" name="token" value="<?= $this->esc($token); ?>" />
          <div class="form-group<?= isset($errors['loginPassword']) ? ' has-error' : ''; ?>">
            <label for="loginPassword" class="control-label col-md-4">New password:</label>
            <div class="col-md-6">
              <input type="password" name="loginPassword" id="loginPassword" class="form-control" placeholder="New password" value="<?php if (isset($formVars['loginPassword'])) echo $this->esc($formVars['loginPassword']); ?>" maxlength="30" required autofocus />
              <?php if (isset($errors['loginPassword'])) :?>
                <div class="alert alert-danger"><?= $this->esc($errors['loginPassword']); ?></div>
              <?php endif; ?>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6 col-md-offset-4">
              <label class="control-label">
                <input type="checkbox" onchange="togglePassword(this)" />
                Show password
              </label>
            </div>
          </div>
          <script>
          function togglePassword(val) {
            $('#loginPassword').attr('type', val.checked ? 'text' : 'password');
          }
          </script>


          <div class="form-group">
            <div class="col-md-6 col-md-offset-4">
              <input type="submit" class="btn btn-primary" value="Reset My Password" />
            </div>
          </div>
        </form>
      </div><!-- col-md-8 col-md-offset-2 -->
    </div><!-- row -->

<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
