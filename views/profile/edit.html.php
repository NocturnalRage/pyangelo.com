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
        <h1>Edit Your Profile</h1>
        <?php
          include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php';
        ?>
        <form method="post" action="/profile/update" class="form-horizontal">
          <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
          <div class="<?= isset($errors['given_name']) ? ' has-error' : ''; ?>">
            <label for="given_name" class="control-label">Given Name:</label>
              <input type="input" name="given_name" id="given_name" class="form-control" placeholder="Given name" value="<?php if (isset($formVars['given_name'])) echo $this->esc($formVars['given_name']); ?>" maxlength="100" required autofocus />
              <?php if (isset($errors['given_name'])) :?>
                <div class="alert alert-danger"><?= $this->esc($errors['given_name']); ?></div>
              <?php endif; ?>
          </div>
          <div class="<?= isset($errors['family_name']) ? ' has-error' : ''; ?>">
            <label for="family_name" class="control-label">Family Name:</label>
              <input type="input" name="family_name" id="family_name" class="form-control" placeholder="Family name" value="<?php if (isset($formVars['family_name'])) echo $this->esc($formVars['family_name']); ?>" maxlength="100" required />
              <?php if (isset($errors['family_name'])) :?>
                <div class="alert alert-danger"><?= $this->esc($errors['family_name']); ?></div>
              <?php endif; ?>
          </div>
          <div class="<?= isset($errors['email']) ? ' has-error' : ''; ?>">
            <label for="email" class="control-label">Email:</label>
              <input type="email" name="email" id="email" class="form-control" placeholder="Email" value="<?php if (isset($formVars['email'])) echo $this->esc($formVars['email']); ?>" maxlength="100" required />
              <?php if (isset($errors['email'])) :?>
                <div class="alert alert-danger"><?= $this->esc($errors['email']); ?></div>
              <?php endif; ?>
          </div>
          <div class="add-bottom<?= isset($errors['country_code']) ? ' has-error' : ''; ?>">
            <label for="country_code" class="control-label">Country:</label>
            <select id="country_code" name="country_code" class="form-control">
            <?php foreach ($countries as $country): ?>
              <option <?php if ($country['country_code'] == ($formVars['country_code'] ?? '')) echo 'selected'; ?> value="<?= $this->esc($country['country_code']); ?>"><?= $this->esc($country['country_name']); ?></option>
            <?php endforeach; ?>
            </select>
            <?php if (isset($errors['country_code'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['country_code']); ?></div>
            <?php endif; ?>
          </div>
          <div>
            <input type="submit" class="btn btn-primary" value="Update My Profile" />
          </div>
        </form>
        <div>
          <hr />
          <p>Want to change your profile photo? We pull from <a href="https://gravatar.com">Gravatar</a>.</p>
        </div>
      </div><!-- col-md-9 -->
    </div><!-- row -->

<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
