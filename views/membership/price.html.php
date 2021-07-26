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
                <div id="card-element-success"></div>
            </div>
            <div class="add-bottom">
              <button class="btn btn-lg btn-primary" id="submit-payment-btn" type="submit">
                Subscribe for <?= $numberFormatter->formatCurrency(($priceInCents / $currency['stripe_divisor']), $currency['currency_code']) ?> per month
              </button>
            </div>
          <?php endif ?>
        </div><!-- panel -->
      </div><!-- col-md-4 -->
