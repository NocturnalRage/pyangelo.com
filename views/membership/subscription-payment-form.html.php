<?php
  include __DIR__ . '/../layout/header.html.php';
  include __DIR__ . '/../layout/navbar.html.php';
?>
  <script src="https://js.stripe.com/v3/"></script>
  <div class="container">
    <div class="row">
      <div class="col-md-12 text-center">
        <h3>
          <?= $this->esc($pyangeloPrice['product_name']); ?>
        </h3>
        <p>
          <?= $this->esc($pyangeloPrice['product_description']); ?>
        </p>
        <?php
          $displayPrice = $numberFormatter->formatCurrency(($stripePrice->unit_amount / $pyangeloPrice['stripe_divisor']), $pyangeloPrice['currency_code']);
        ?>
        <h1><?= $displayPrice ?> per Month</h1>
      </div><!-- col-md-12 -->
    </div><!-- row -->

    <div class="row">
      <div id="payment-container" class="col-md-12 text-center">
        <h2 class="payment-container-text add-bottom">Payment Information</h2>
        <form id="payment-form" data-crsf-token="<?= $this->esc($personInfo['crsfToken']); ?>" data-price-id="<?= $this->esc($stripePrice->id); ?>" data-stripe-publishable-key="<?= $this->esc($stripePublishableKey); ?>">
          <div id="payment-element" class="add-bottom">
            <!-- Elements will create form elements here -->
          </div>
          <button class="btn btn-lg" id="pay-btn" type="submit">
            Subscribe <?= $displayPrice ?> per Month
          </button>
          <div id="stripe-error-message">
            <!-- Display error message to your customers here -->
          </div>
          <div class="payment-container-text">
            <p>Prices are in <?= $this->esc($pyangeloPrice['currency_description']) ?></p>
          </div>
        </form>
      </div><!-- col-md-12 -->
    </div><!-- row -->

    <?php include __DIR__ . '/premium-membership-faq.html.php'; ?>
    <?php include __DIR__ . '/../layout/footer.html.php'; ?>
  </div><!-- container -->
  <script src="<?= mix('js/subscription.js'); ?>"></script>
</body>
</html>
