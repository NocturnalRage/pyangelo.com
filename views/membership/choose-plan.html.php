<?php
  include __DIR__ . '/../layout/header.html.php';
  include __DIR__ . '/../layout/navbar.html.php';
?>
  <div class="container">
    <?php include __DIR__ . '/../layout/flash.html.php'; ?>
    <div class="row">
      <div class="col-md-12 text-center">
        <h1>Get Full Access to Every Tutorial</h1>
        <?php
          include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php';
        ?>
        <p>
          Become a PyAngelo Premium Member today by signing up to a monthly subscription and get access to all PyAngelo video tutorials including all new content that is uploaded.
        </p>
        <hr />
      </div>
    </div><!-- row -->

    <div class="row">
      <div class="col-md-12">
        <h1 class="text-center">Choose a Monthly Plan</h1>
        <hr />
      </div>
    </div><!-- row -->

    <div class="row">
      <?php foreach ($membershipPrices as $price): ?>
        <div class="col-md-6 text-center">
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h3>
                <?= $this->esc($price['product_name']); ?>
              </h3>
            </div><!-- panel-heading -->
            <div class="panel-body">
              <h1><?= $numberFormatter->formatCurrency(($price['price_in_cents'] / $currency['stripe_divisor']), $currency['currency_code']) ?> per Month</h1>
              <p>
                <?= $this->esc($price['product_description']); ?>
              </p>
              <a href="/subscription-payment-form/<?= $this->esc($price['stripe_price_id']); ?>">
                <button class="btn btn-lg btn-primary" id="choose-price-btn" type="submit">
                  Choose <?= $this->esc($price['product_name']); ?> Monthly Plan
                </button>
              </a>
            </div><!-- panel-body -->
          </div><!-- panel -->
        </div><!-- col-md-4 -->
      <?php endforeach; ?>
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
</body>
</html>
