<?php
include __DIR__ . DIRECTORY_SEPARATOR . 'layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . 'layout/navbar.html.php';
?>
  <div class="container">
  
    <h1 class="text-center">Welcome Back!</h1>
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . 'layout/flash.html.php';
    ?>
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <form id="loginForm" method="post" action="/login-validate" class="form-horizontal">
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
          <div class="form-group<?= isset($errors['loginPassword']) ? ' has-error' : ''; ?>">
            <label for="loginPassword" class="control-label col-md-4">Password:</label>
            <div class="col-md-6">
              <input type="password" name="loginPassword" id="loginPassword" class="form-control" placeholder="Password" value="<?php if (isset($formVars['loginPassword'])) echo $this->esc($formVars['loginPassword']); ?>" maxlength="30" required />
              <?php if (isset($errors['loginPassword'])) :?>
                <div class="alert alert-danger"><?= $this->esc($errors['loginPassword']); ?></div>
              <?php endif; ?>
            </div>
          </div>

          <div class="form-group">
            <div class="col-md-6 col-md-offset-4">
              <div class="checkbox">
                <label>
                  <input type="checkbox" id="rememberme" name="rememberme" value="y" /> Remember Me
                </label>
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="col-md-6 col-md-offset-4">
              <button
                type="submit"
                class="g-recaptcha btn btn-primary"
                data-sitekey="<?= $this->esc($recaptchaKey); ?>"
                data-callback='onSubmit'
                data-action='registerwithversion3'
              >
                <i class="fa fa-sign-in" aria-hidden="true"></i> Login To Your Account
              </button>
            </div>
          </div>
        </form>
        <hr />
        <p class="text-center">Don't have an account? <a href="/register">Create one</a> in 30 seconds.</p>
        <p class="text-center"><a href="/forgot-password">Forgot your password?</a></p>
      </div><!-- col-md-8 col-md-offset-2 -->
    </div><!-- row -->

<?php
include __DIR__ . DIRECTORY_SEPARATOR . 'layout/footer.html.php';
?>
  </div><!-- container -->
  <script src="https://www.google.com/recaptcha/api.js"></script>
  <script>
    function onSubmit(token) {
      document.getElementById("loginForm").submit();
    }
  </script>
</body>
</html>
