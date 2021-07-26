<?php
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/header.html.php';
include __DIR__ . DIRECTORY_SEPARATOR . '../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <?php
        include __DIR__ . DIRECTORY_SEPARATOR . '/../layout/flash.html.php';
        include __DIR__ . DIRECTORY_SEPARATOR . '/admin-menu.html.php';
      ?>
      <div class="col-md-9">
        <?php
          include __DIR__ . DIRECTORY_SEPARATOR . '/person.html.php';
        ?>
        <?php if ($person['premium_status_boolean'] == 1) : ?>
          <form action="/admin/update-premium-end-date" method="POST">
            <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
            <input type="hidden" name="months" value="0" />
            <input type="hidden" name="person_id" value="<?= $this->esc($person['person_id']) ?>" />
            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to revoke access for this user?')">
              <i class="fa fa-calendar-minus-o" aria-hidden="true"></i> Revoke Access
            </button>
          </form>
        <?php else : ?>
          <form action="/admin/update-premium-end-date" method="POST">
            <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
            <input type="hidden" name="months" value="1" />
            <input type="hidden" name="person_id" value="<?= $this->esc($person['person_id']) ?>" />
            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to grant access for 1 month?')">
              <i class="fa fa-calendar-plus-o" aria-hidden="true"></i> Grant Access for 1 Month
            </button>
          </form>
          <br/>
          <form action="/admin/update-premium-end-date" method="POST">
            <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
            <input type="hidden" name="months" value="3" />
            <input type="hidden" name="person_id" value="<?= $this->esc($person['person_id']) ?>" />
            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to grant access for 3 months?')">
              <i class="fa fa-calendar-plus-o" aria-hidden="true"></i> Grant Access for 3 Months
            </button>
          </form>
          <br/>
          <form action="/admin/update-premium-end-date" method="POST">
            <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
            <input type="hidden" name="months" value="12" />
            <input type="hidden" name="person_id" value="<?= $this->esc($person['person_id']) ?>" />
            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to grant access for 1 year?')">
              <i class="fa fa-calendar-plus-o" aria-hidden="true"></i> Grant Access for 1 Year
            </button>
          </form>
          <br/>
          <form action="/admin/update-premium-end-date" method="POST">
            <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
            <input type="hidden" name="months" value="120" />
            <input type="hidden" name="person_id" value="<?= $this->esc($person['person_id']) ?>" />
            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to grant access for 10 years?')">
              <i class="fa fa-calendar-plus-o" aria-hidden="true"></i> Grant Access for 10 Years
            </button>
          </form>
          <hr />
        <?php endif ?>
        <?php if (empty($subscription)) : ?>
          <h2>This User Doesn't Have an Active Subscription</h2>
        <?php else : ?>
          <h2>This User Has a Subscription</h2>
          <?php if ($subscription['percent_off'] > 0) : ?>
            <h3>They have a permanent discount of <?= $this->esc($subscription['percent_off']) ?>%</h3>
          <?php endif ?>
          <?php if ($subscription['cancel_at_period_end'] == 1) : ?>
            <p>This subscription has been cancelled.</p>
          <?php endif ?>
          <ul class="list-group">
            <li class="list-group-item">
              Cost:
              <?= $subscription['currency_symbol'] . $numberFormatter->formatCurrency((($subscription['price_in_cents'] * ((100 - $subscription['percent_off']) / 100)) / $subscription['stripe_divisor']), $subscription['currency_code']) ?> per month
            </li>
            <li class="list-group-item">
              Started:
              <?= $this->esc($subscription['premiumMemberSince']) ?>
            </li>
            <?php if ($subscription['cancel_at_period_end'] != 1) : ?>
              <li class="list-group-item">
                Next Payment:
                <?= $this->esc($subscription['nextPaymentDate']) ?>
              </li>
            <?php else : ?>
              <li class="list-group-item">
                Subscription ends:
                <?= $this->esc($subscription['nextPaymentDate']) ?>
              </li>
            <?php endif ?>
          </ul><!-- list-group -->
          <?php if ($subscription['cancel_at_period_end'] != 1) : ?>
            <div>
              <hr />
              <h1>Cancel This Subscription</h1>
              <p>By clicking the button below you are cancelling this person's stripe subscription. They will still have access for the period that have paid for which ends <?= $this->esc($subscription['nextPaymentDate']) ?>. If you wish to stop all access you need to cancel the membership and then revoke access above.</p>
              <form action="/admin/cancel-subscription" method="POST">
                <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
                <input type="hidden" name="person_id" value="<?= $this->esc($person['person_id']) ?>" />
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this membership?')">
                  <i class="fa fa-times" aria-hidden="true"></i> Cancel This Subscription
                </button>
              </form>
            </div>
          <?php endif ?>
        <?php endif ?>

        <?php if (! empty($pastSubscriptions)) : ?>
          <?php         include __DIR__ . '/../profile/past-subscriptions.html.php'; ?>
        <?php endif ?>

        <?php if (empty($payments)) : ?>
          <h2>No Payments Have Been Made By This Person</h2>
        <?php else : ?>
          <?php         include __DIR__ . '/../profile/payment-history.html.php'; ?>
        <?php endif ?>
      </div><!-- col-md-9 -->
    </div><!-- row -->
    <?php
      include __DIR__ . DIRECTORY_SEPARATOR . '../layout/footer.html.php';
    ?>
  </div><!-- container -->
</body>
</html>
