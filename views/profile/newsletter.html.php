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
        <h1>PyAngelo Email Newsletter</h1>
        <?php
          include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php';
        ?>
        <?php if ($subscribed) : ?>
          <p>
            You are currently subscribed to the PyAngelo email newsletters.
            Every newsletter we send out will contain a one click unsubscribe
            link. You can also unsubscribe using the form below. But why
            would you want to? We only send out amazing newsletters that
            everybody finds interesting!
          </p>
        <?php else : ?>
          <p>
            You are not currently subscribed to the PyAngelo email
            newsletters. You can rectify this situation using the form
            below. We send out amazing newsletters that everybody finds
            interesting!
          </p>

        <?php endif; ?>
        <form method="post" action="/newsletter-validate" class="form-horizontal">
          <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken'] ?>" />
          <div class="add-bottom">
            <label class="control-label">
              <input type="checkbox" name="newsletter" id="newsletter" value="yes"<?= $subscribed ? ' checked' : ''; ?> />
              Subscribe to the PyAngelo Newsletters (Leave blank to unsubscribe)
            </label>
          </div>

          <div>
            <input type="submit" class="btn btn-info" value="Update My Preference" />
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
