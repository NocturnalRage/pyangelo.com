<?php
include __DIR__ . '/../layout/header.html.php';
include __DIR__ . '/../layout/navbar.html.php';
?>
  <?php if ($personInfo['loggedIn'] && $hasActiveSubscription) : ?>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
      let stripe = Stripe('<?= $stripePublishableKey ?>');
      let elements = stripe.elements();
    </script>
  <?php endif ?>
  <div class="container">
    <div class="row">
      <?php
        include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php';
        include __DIR__ . DIRECTORY_SEPARATOR . '/profile-menu.html.php';
      ?>
        <?php if ($personInfo['loggedIn'] && $hasActiveSubscription) : ?>
          <div class="col-md-9">
            <h1>Update Your Credit Card</h1>
            <p>Currently the last 4 digits of the credit card used for your payments are: <?= $person["last4"] ?></p>
            <p>If you would like to update the credit card associated with your subscription simply fill in the form below and click the "Update Card Details" button. And don't worry, your card information will be securely sent to our payment provider and never directly touch our servers.</p>
          </div><!-- col-md-9 -->
          <div class="col-md-9 text-center">
            <hr />
            <h3 class="add-bottom">New Card Details</h3>
            <div class="add-bottom">
              <form id="payment-form" data-crsf-token="<?= $personInfo['crsfToken'] ?>">
                <div id="card-element" class="form-control"></div>
                <div id="card-element-errors" class="alert alert-danger" role="alert"></div>
            </div>
            <div class="add-bottom">
              <button class="btn btn-lg btn-primary" id="submit-details-btn" type="submit">
                Update Card Details
              </button>
            </div>
          </div><!-- col-md-9 -->
        <?php else : ?>
          <div class="col-md-9">
            <h1>You Don't Have an Active Subscription</h1>
            <p>If you <a href="/choose-plan">subscribe to one of our monthly plans</a>, your credit card details will be securely stored with our payment provider, Stripe. Once you've done that you'll be able to update your credit card details via this page.</p>
          </div><!-- col-md-9 -->
        <?php endif ?>
    </div><!-- row -->
<?php
include __DIR__ . '/../layout/footer.html.php';
?>
  </div><!-- container -->
  <?php if ($personInfo['loggedIn'] && $hasActiveSubscription) : ?>
    <script src="<?= mix('js/new-payment-method.js'); ?>"></script>
  <?php endif ?>
</body>
</html>
