<?php
include __DIR__ . DIRECTORY_SEPARATOR . 'layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . 'layout/navbar.html.php';
?>
  <div class="container">
  
    <h3 class="text-center">How Can We Help?</h3>
    <p class="text-center">
    Hi! Here at PyAngelo we welcome all feedback and suggestions about our
    tutorials and other content, don't hesitate to email us about any topic,
    and we'll get back to you as soon as possible! However, we do appreciate
    it if you search through our tutorials and blogs to see if you can find
    one that helps to answer your question. For any other inquiries or
    feedback, please use the contact form below. Cheers!
    </p>
    <hr />
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . 'layout/flash.html.php';
    ?>
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <form id="contactUsForm" method="post" action="/contact-validate" class="form-horizontal">
          <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
          <div class="form-group<?= isset($errors['name']) ? ' has-error' : ''; ?>">
            <label for="name" class="control-label">Name:</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="<?php if (isset($formVars['name'])) echo $this->esc($formVars['name']); ?>" maxlength="100" required autofocus />
            <?php if (isset($errors['name'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['name']); ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group<?= isset($errors['email']) ? ' has-error' : ''; ?>">
            <label for="email" class="control-label">Email:</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Email" value="<?php if (isset($formVars['email'])) echo $this->esc($formVars['email']); ?>" maxlength="100" required />
            <?php if (isset($errors['email'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['email']); ?></div>
            <?php endif; ?>
          </div>
          <div class="form-group<?= isset($errors['inquiry']) ? ' has-error' : ''; ?>">
            <label for="inquiry" class="control-label">Inquiry:</label>
            <textarea name="inquiry" maxlength="1000" id="inquiry" class="form-control" placeholder="What's on your mind?" rows="8" /><?= $formVars['inquiry'] ?? '' ?></textarea>
            <?php if (isset($errors['inquiry'])) :?>
              <div class="alert alert-danger"><?= $this->esc($errors['inquiry']); ?></div>
            <?php endif; ?>
          </div>

          <div class="form-group cf-turnstile" data-sitekey="<?= $_ENV['TURNSTILE_SITE_KEY'] ?>"></div>

          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-envelope" aria-hidden="true"></i> Contact Us
            </button>
          </div>
        </form>
      </div><!-- col-md-8 col-md-offset-2-->
    </div><!-- row -->

<?php
include __DIR__ . DIRECTORY_SEPARATOR . 'layout/footer.html.php';
?>
  </div><!-- container -->
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer ></script>
</body>
</html>
