<?php
include __DIR__ . '/../layout/header.html.php';
include __DIR__ . '/../layout/navbar.html.php';
?>
  <div class="container">
    <div class="row">
      <?php
        include __DIR__ . DIRECTORY_SEPARATOR . '../layout/flash.html.php';
        include __DIR__ . DIRECTORY_SEPARATOR . '/profile-menu.html.php';
      ?>
      <div class="col-md-9">
        <?php if (empty($subscription)) : ?>
          <h1>You Don't Have an Active Subscription</h1>
          <p>If you <a href="/premium-membership">join our monthly premium membership</a>, your subscription details will be shown here, plus you'll start learning amazing ways to solve problems using coding!</p>
        <?php else : ?>
          <h1>You are a PyAngelo Premium Member</h1>
          <?php if ($subscription['percent_off'] > 0) : ?>
          <h3>You have a permanent discount of <?= $this->esc($subscription['percent_off']) ?>%</h3>
          <?php endif ?>
          <?php if ($subscription['cancel_at_period_end'] == 1) : ?>
            <p>This subscription has been cancelled.</p>
          <?php endif ?>
          <ul class="list-group">
            <li class="list-group-item">
              Cost:
              <?= $numberFormatter->formatCurrency((($subscription['price_in_cents'] * ((100 - $subscription['percent_off']) / 100)) / $subscription['stripe_divisor']), $subscription['currency_code']) ?> per month
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
          <?php if ($subscription['cancel_at_period_end'] == 1) : ?>
            <h1>Resume Your Subscription</h1>
            <p>You can resume your subscription by clicking the button below. Doing so will put you back onto the plan specified above. Your next payment will be in <?= $this->esc($subscription['nextPaymentDate']) ?>.</p>
            <form action="/resume-subscription" method="POST">
              <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
              <button type="submit" class="btn btn-success">
                <i class="fa fa-refresh" aria-hidden="true"></i> Resume Your Subscription
              </button>
            </form>

          <?php else : ?>
            <div>
              <hr />
              <h1>Cancel Your Subscription</h1>
              <p>If you want to cancel your subscription you can do so by clicking the button below. You'll still have access until the end of the period you paid for which is <?= $this->esc($subscription['nextPaymentDate']) ?>.</p>
              <form action="/cancel-subscription" method="POST">
                <input type="hidden" name="crsfToken" value="<?= $personInfo['crsfToken']; ?>" />
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel your membership?')">
                  <i class="fa fa-times" aria-hidden="true"></i> Cancel Your Subscription
                </button>
              </form>
            </div>
          <?php endif ?>
        <?php endif ?>

        <?php if (! empty($pastSubscriptions)) : ?>
          <?php         include __DIR__ . '/past-subscriptions.html.php'; ?>
        <?php endif ?>

      </div><!-- col-md-9 -->
    </div><!-- row -->
<?php
include __DIR__ . '/../layout/footer.html.php';
?>
  </div><!-- container -->
</body>
</html>
