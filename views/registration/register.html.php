<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
  
    <h3 class="text-center">Good decision. We'll teach you to code.</h3>
    <p class="text-center">Let's set up your free account. Already have one? <a href="/login">Login</a> now.</p>
    <hr />
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php';
    ?>
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <form method="post" action="/register-validate" class="form-horizontal">
          <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
          <input type="hidden" name="time" value="<?= time(); ?>" />
          <div class="form-group<?= isset($errors['givenName']) ? ' has-error' : ''; ?>">
            <label for="givenName" class="control-label col-md-4">First Name:</label>
            <div class="col-md-6">
              <input type="text" name="givenName" id="givenName" class="form-control" placeholder="First Name" value="<?php if (isset($formVars['givenName'])) echo $this->esc($formVars['givenName']); ?>" maxlength="100" required autofocus />
              <?php if (isset($errors['givenName'])) :?>
                <div class="alert alert-danger"><?= $this->esc($errors['givenName']); ?></div>
              <?php endif; ?>
            </div>
          </div>
          <div class="form-group<?= isset($errors['familyName']) ? ' has-error' : ''; ?>">
            <label for="familyName" class="control-label col-md-4">Last Name:</label>
            <div class="col-md-6">
              <input type="text" name="familyName" id="familyName" class="form-control" placeholder="Last Name" value="<?php if (isset($formVars['familyName'])) echo $this->esc($formVars['familyName']); ?>" maxlength="100" required />
              <?php if (isset($errors['familyName'])) :?>
                <div class="alert alert-danger"><?= $this->esc($errors['familyName']); ?></div>
              <?php endif; ?>
            </div>
          </div>
          <div class="form-group<?= isset($errors['email']) ? ' has-error' : ''; ?>">
            <label for="email" class="control-label col-md-4">Email:</label>
            <div class="col-md-6">
              <input type="email" name="email" id="email" class="form-control" placeholder="Email" value="<?php if (isset($formVars['email'])) echo $this->esc($formVars['email']); ?>" maxlength="100" required />
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
          <hr />
          <div class="form-group">
            <div class="col-md-12">
              <label>
                <input type="checkbox" id="consent" name="consent" value="1" <?= $this->esc(($formVars['consent'] ?? 0) == 1 ? 'checked' : '') ?> />
                I agree to the <a href="/terms-of-use">PyAngelo Terms of Use</a> and consent to PyAngelo using my data as per the <a href="/privacy-policy">Privacy Policy</a>
              </label>
              <?php if (isset($errors['consent'])) :?>
                <div class="alert alert-danger"><?= $this->esc($errors['consent']); ?></div>
              <?php endif; ?>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6 col-md-offset-4">
              <button type="submit" class="btn btn-primary">
                <i class="fa fa-user-plus" aria-hidden="true"></i> Create My Free Account
              </button>
            </div>
          </div>
        </form>
      </div><!-- col-md-6 -->
    </div><!-- row -->

<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
