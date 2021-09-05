      <div class="col-md-3">
        <div class="well">
          <h3>Settings</h3>
          <div class="list-group">
            <a href="/profile" class="list-group-item<?= $activeLink == 'profile' ? ' active' : '' ?>"><i class="fa fa-user fa-fw"></i> Profile</a>
            <a href="/favourites" class="list-group-item<?= $activeLink == 'favourites' ? ' active' : '' ?>"><i class="fa fa-star fa-fw"></i> Favourites</a>
            <a href="/newsletter" class="list-group-item<?= $activeLink == 'newsletter' ? ' active' : '' ?>"><i class="fa fa-envelope fa-fw"></i> Email Newsletter</a>
            <a href="/password" class="list-group-item<?= $activeLink == 'password' ? ' active' : '' ?>"><i class="fa fa-key fa-fw"></i> Password</a>
          </div><!-- list-group -->
        </div><!-- well -->

        <div class="well">
          <h3>Classes</h3>
          <div class="list-group">
            <a href="/classes/teacher" class="list-group-item<?= $activeLink == 'teacher' ? ' active' : '' ?>"><i class="fa fa-university fa-fw"></i> Teacher</a>
            <a href="/classes/student" class="list-group-item<?= $activeLink == 'student' ? ' active' : '' ?>"><i class="fa fa-graduation-cap fa-fw"></i> Student</a>
          </div><!-- list-group -->
        </div><!-- well -->

        <div class="well">
          <h3>Billing</h3>
          <div class="list-group">
            <a href="/subscription" class="list-group-item<?= $activeLink == 'subscription' ? ' active' : '' ?>"><i class="fa fa-shopping-bag fa-fw"></i> Subscription</a>
            <a href="/invoices" class="list-group-item<?= $activeLink == 'invoices' ? ' active' : '' ?>"><i class="fa fa-file-text-o fa-fw"></i> Invoices</a>
            <a href="/payment-method" class="list-group-item<?= $activeLink == 'payment-method' ? ' active' : '' ?>"><i class="fa fa-credit-card fa-fw"></i> Payment Method</a>
          </div><!-- list-group -->
        </div><!-- well -->
      </div><!-- col-md-3 -->
