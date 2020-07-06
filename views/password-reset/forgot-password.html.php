<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
  
    <h1 class="text-center">You've Forgot Your Password</h1>
    <p class="text-center">
    No problems. Just enter the email address you used to create your account
    and we'll send you a link to reset your password.
    </p>
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php';
    ?>
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <form method="post" action="/forgot-password-validate" class="form-horizontal">
          <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
          <div class="form-group<?= isset($errors['email']) ? ' has-error' : ''; ?>">
            <label for="email" class="control-label col-md-4">Email:</label>
            <div class="col-md-6">
              <input type="email" name="email" id="email" class="form-control" placeholder="Email" value="<?php if (isset($formVars['email'])) echo $this->esc($formVars['email']); ?>" maxlength="100" required autofocus />
              <?php if (isset($errors['email'])) :?>
                <div class="alert alert-danger"><?= $this->esc($errors['email']); ?></div>
              <?php endif; ?>
            </div>
          </div>

          <div class="form-group">
            <div class="col-md-6 col-md-offset-4">
              <input type="submit" class="btn btn-primary" value="Send me a password reset link" />
            </div>
          </div>
        </form>
        <hr />
        <p class="text-center">Did you suddenly remember your password? <a href="/login">Login</a></p>
      </div><!-- col-md-8 col-md-offset-2 -->
    </div><!-- row -->

<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
