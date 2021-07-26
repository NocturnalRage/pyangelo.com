<?php
  include __DIR__ . '/../layout/header.html.php';
  include __DIR__ . '/../layout/navbar.html.php';
?>
  <?php if ($personInfo['loggedIn'] && ! $hasActiveSubscription) : ?>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
      let stripe = Stripe('<?= $stripePublishableKey ?>');
      let elements = stripe.elements();
    </script>
  <?php endif ?>
  <div class="container">
    <?php if (! $personInfo['loggedIn']) : ?>
      <div class="row">
        <div class="col-md-12 text-center add-bottom">
            <h3>Do you already have a free PyAngelo account?</h3>
            <p>
              You need a free account before you can become a Premium Member.
              If you already have one then sign in, otherwise create one now, it
              takes less than 60 seconds.
            </p>
        </div><!-- col-md-12 -->
      </div><!-- row -->
      <div class="row">
        <div class="col-sm-4 col-sm-offset-2 text-center">
          <h5>Yes, I'm already a free member</h5>
          <a href="/login" class="btn btn-primary">
            <i class="fa fa-sign-in" aria-hidden="true"></i> Login To Your Account</a>
        </div>
        <div class="col-sm-4 text-center">
          <h5>No, I'll create my free account now</h5>
          <a href="/register" class="btn btn-success">
            <i class="fa fa-user-plus" aria-hidden="true"></i> Create Your Free Account</a>
        </div>
      </div><!-- row -->
      <div class="row">
        <div class="col-md-12">
          <hr />
        </div>
      </div><!-- row -->
    <?php endif ?>
    <div class="row">
      <div class="col-md-12">
        <?php
          include __DIR__ . DIRECTORY_SEPARATOR . '/../layout/flash.html.php';
        ?>
        <?php if ($hasActiveSubscription) : ?>
          <h1 class="text-center">You are already a Premium Member</h1>
        <?php else : ?>
          <h1 class="text-center">Become a Premium Member</h1>
        <?php endif ?>
      </div>
    </div><!-- row -->
    <div class="row">
      <div class="col-sm-12">
        <p class="text-center">
          Become a PyAngelo Premium Member today by signing up to a monthly subscription and get access to all PyAngelo video tutorials including all new content that is uploaded.
        </p>
      </div>
    </div><!-- row -->
    <?php
      $price = $membershipPrices[0];
    ?>
    <div class="row">
      <div class="col-md-8 col-md-offset-2 text-center">
        <div class="panel panel-primary">
          <div class="panel-heading">
            <h3>
              <?= $this->esc($price['product_name']); ?>
            </h3>
          </div><!-- panel-heading -->
          <div class="panel-body">
            <?php
              $priceInCents = $price['price_in_cents'];
            ?>
            <h1><?= $numberFormatter->formatCurrency(($priceInCents / $currency['stripe_divisor']), $currency['currency_code']) ?> per Month</h1>
          </div><!-- panel-body -->
          <?php if ($personInfo['loggedIn'] && ! $hasActiveSubscription) : ?>
            <hr />
            <h3 class="add-bottom">Payment Information</h3>
            <div class="add-bottom">
              <form id="payment-form" data-crsf-token="<?= $personInfo['crsfToken'] ?>" data-price-id="<?= $price['stripe_price_id'] ?>">
                <div id="card-element" class="form-control"></div>
                <div id="card-element-errors" class="alert alert-danger" role="alert"></div>
            </div>
            <div class="add-bottom">
              <button class="btn btn-lg btn-primary" id="submit-payment-btn" type="submit">
                Subscribe for <?= $numberFormatter->formatCurrency(($priceInCents / $currency['stripe_divisor']), $currency['currency_code']) ?> per month
              </button>
            </div>
          <?php endif ?>
        </div><!-- panel -->
      </div><!-- col-md-4 -->
    </div><!-- row -->
    <div class="row">
      <div class="col-md-12 text-center">
        <p>Prices are in <?= $this->esc($currency['currency_description']) ?></p>
        <hr />
      </div><!-- col-md-12 -->
    </div><!-- row -->

    <?php include __DIR__ . '/premium-membership-faq.html.php'; ?>
    <?php include __DIR__ . '/../layout/footer.html.php'; ?>
  </div><!-- container -->
  <?php if ($personInfo['loggedIn'] && ! $hasActiveSubscription) : ?>
    <script src="<?= mix('js/subscription.js'); ?>"></script>
  <?php endif ?>
</body>
</html>
